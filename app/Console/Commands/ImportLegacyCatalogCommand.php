<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ImportLegacyCatalogCommand extends Command
{
    protected $signature = 'catalog:import-legacy
        {--dry-run : Toon wat er wordt gewijzigd zonder de database of opslag aan te passen}
        {--source= : Alternatief WooCommerce Store API-endpoint}';

    protected $description = 'Importeer de productcatalogus van de voormalige WooCommerce-webshop';

    public function handle(): int
    {
        $response = Http::retry(3, 500)
            ->timeout(30)
            ->acceptJson()
            ->get((string) ($this->option('source') ?: 'https://oliehandelvandeutekom.nl/wp-json/wc/store/v1/products?per_page=100'));

        if ($response->failed() || ! is_array($response->json())) {
            $this->error('De productcatalogus kon niet worden opgehaald.');

            return self::FAILURE;
        }

        $categories = Category::whereIn('slug', ['kachels', 'vloeistoffen', 'accessoires'])
            ->get()
            ->keyBy('slug');

        if ($categories->count() !== 3) {
            $this->error('De categorieën Kachels, Vloeistoffen en Accessoires zijn vereist.');

            return self::FAILURE;
        }

        $created = 0;
        $updated = 0;
        $images = 0;
        $failedImages = 0;
        $dryRun = (bool) $this->option('dry-run');

        foreach ($response->json() as $legacyProduct) {
            $name = $this->plainText((string) ($legacyProduct['name'] ?? ''));

            if ($name === '') {
                continue;
            }

            $legacyId = (int) ($legacyProduct['id'] ?? 0);
            $slug = $this->slugFor($legacyId, $name);
            $product = Product::where('slug', $slug)->first();
            $action = $product ? 'bijwerken' : 'aanmaken';

            $this->line(sprintf(
                '<fg=%s>%s</> %s',
                $product ? 'yellow' : 'green',
                $product ? 'UPDATE' : 'CREATE',
                $name,
            ));

            if ($dryRun) {
                $product ? $updated++ : $created++;
                continue;
            }

            $categorySlug = $this->categorySlug($legacyProduct);
            $category = $categories[$categorySlug];
            $imagePath = $product?->image;
            $imageUrl = data_get($legacyProduct, 'images.0.src');

            if ($imageUrl && (! $imagePath || ! Storage::disk('public')->exists($imagePath))) {
                try {
                    $imagePath = $this->storeImage((string) $imageUrl, $slug);
                    $images++;
                } catch (Throwable $exception) {
                    report($exception);
                    $failedImages++;
                    $this->warn("  Afbeelding overgeslagen: {$imageUrl}");
                }
            }

            $data = [
                'name' => $name,
                'slug' => $slug,
                'price' => ((int) data_get($legacyProduct, 'prices.price', 0)) / (10 ** (int) data_get($legacyProduct, 'prices.currency_minor_unit', 2)),
                'category_id' => $category->id,
                'type' => $category->type,
                'brand' => $this->brand($name),
                'model_type' => $this->modelType($name, $categorySlug),
                'used' => false,
                'description' => $this->description($legacyProduct),
                'short_description' => $this->shortDescription($legacyProduct),
                'image' => $imagePath,
                'active' => true,
                'featured' => false,
            ];

            DB::transaction(function () use ($product, $data): void {
                $product
                    ? $product->update($data)
                    : Product::create($data);
            });

            $product ? $updated++ : $created++;
        }

        $this->newLine();
        $this->info("Klaar: {$created} aangemaakt, {$updated} bijgewerkt, {$images} afbeeldingen opgeslagen.");

        if ($failedImages > 0) {
            $this->warn("{$failedImages} afbeeldingen konden niet worden opgeslagen.");
        }

        return self::SUCCESS;
    }

    private function slugFor(int $legacyId, string $name): string
    {
        return match ($legacyId) {
            2528 => 'turboheating-20l-in-nieuwe-jerrycan',
            334 => 'zibro-laserkachel-lc-150',
            default => Str::slug($name),
        };
    }

    private function categorySlug(array $product): string
    {
        $categories = collect($product['categories'] ?? [])
            ->pluck('name')
            ->map(fn ($name) => Str::lower($this->plainText((string) $name)));

        if ($categories->contains(fn ($name) => str_contains($name, 'toebehoren') || str_contains($name, 'gereedschap'))) {
            return 'accessoires';
        }

        if ($categories->contains(fn ($name) => str_contains($name, 'kachel') && ! str_contains($name, 'brandstof'))) {
            return 'kachels';
        }

        return 'vloeistoffen';
    }

    private function brand(string $name): ?string
    {
        $lower = Str::lower($name);

        return match (true) {
            str_starts_with($lower, 'qlima') => 'Qlima',
            str_starts_with($lower, 'zibro') => 'Zibro',
            str_starts_with($lower, 'turboheating') => 'TurboHeating',
            str_starts_with($lower, 'firelux') => 'Firelux',
            str_starts_with($lower, 'adblue') => 'AdBlue',
            default => null,
        };
    }

    private function modelType(string $name, string $categorySlug): ?string
    {
        if ($categorySlug !== 'kachels') {
            return null;
        }

        $lower = Str::lower($name);

        return match (true) {
            str_contains($lower, 'gas') => 'Gaskachel',
            str_contains($lower, 'pellet') || str_contains($lower, 'viola') => 'Pelletkachel',
            str_contains($lower, 'kous') || preg_match('/\br\s?4224/', $lower) === 1 => 'Kouskachel',
            default => 'Laserkachel',
        };
    }

    private function description(array $product): ?string
    {
        $description = $this->plainText((string) ($product['description'] ?? ''));

        return $description !== '' ? $description : null;
    }

    private function shortDescription(array $product): ?string
    {
        $short = $this->plainText((string) ($product['short_description'] ?? ''));

        if ($short === '') {
            $short = Str::limit($this->description($product) ?? '', 300, '…');
        }

        return $short !== '' ? Str::limit($short, 500, '…') : null;
    }

    private function plainText(string $value): string
    {
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = preg_replace('/\[\/?et_pb_[^\]]*\]/iu', ' ', $value) ?? $value;
        $value = strip_tags($value);
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return trim($value);
    }

    private function storeImage(string $url, string $slug): string
    {
        $response = Http::retry(3, 500)->timeout(30)->get($url);
        $response->throw();

        $extension = Str::lower(pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        $extension = in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true) ? $extension : 'jpg';
        $path = "products/legacy-{$slug}.{$extension}";

        Storage::disk('public')->put($path, $response->body());

        return $path;
    }
}
