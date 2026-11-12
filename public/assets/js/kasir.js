(function () {
    'use strict';

    var input = document.getElementById('cariProduk');
    var grid = document.getElementById('gridProduk');
    var kosong = document.getElementById('produkKosong');

    if (!input || !grid || !kosong) {
        return;
    }

    var kartu = Array.prototype.slice.call(grid.querySelectorAll('.product-card'));

    input.addEventListener('input', function () {
        var kata = input.value.trim().toLowerCase();
        var tampil = 0;

        kartu.forEach(function (item) {
            var nama = (item.getAttribute('data-nama') || '').toLowerCase();
            var cocok = kata === '' || nama.indexOf(kata) !== -1;

            item.hidden = !cocok;

            if (cocok) {
                tampil += 1;
            }
        });

        kosong.hidden = tampil !== 0;
    });
})();
