import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('toast', {
    show: false,
    message: '',
    _timer: null,
    open(msg) {
        this.message = msg;
        this.show = true;
        clearTimeout(this._timer);
        this._timer = setTimeout(() => { this.show = false; }, 3000);
    },
});

Alpine.start();
