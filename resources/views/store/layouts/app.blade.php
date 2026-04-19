<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>CiCo Ingenieros</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">



    <style>
        body {
            background: #f4f6f9;
        }

        /* 🔥 NAVBAR PRO */
        .navbar {
            background: #f4f6f9;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 12px 0;
        }

        .navbar-brand {
            color: #2e1a47 !important;
            font-weight: bold;
            font-size: 22px;
        }

        .navbar a {
            color: #333 !important;
            font-weight: 500;
        }

        .navbar a:hover {
            color: #00c897 !important;
        }

        /* 🔍 BUSCADOR */
        .search-box input {
            border-radius: 30px;
            border: 1px solid #ddd;
            padding: 8px 15px;
        }

        .search-box input:focus {
            border-color: #00c897;
            box-shadow: 0 0 0 0.1rem rgba(0, 200, 151, .25);
        }

        /* HERO */
        .hero {
            background: linear-gradient(135deg, #2e1a47, #00c897);
            color: #fff;
            padding: 100px 0;
        }

        /* CARDS */
        .card-product {
            border: none;
            border-radius: 14px;
            overflow: hidden;
            transition: all .3s;
            background: #fff;
        }

        .card-product:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        }

        .card-product img {
            height: 180px;
            object-fit: cover;
        }

        /* PRECIO */
        .price {
            color: #00c897;
            font-weight: bold;
            font-size: 18px;
        }

        /* BOTONES */
        .btn-primary {
            background: #2e1a47;
            border: none;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background: #00c897;
        }

        /* CATEGORÍAS */
        .category-card {
            border-radius: 14px;
            transition: 0.3s;
            cursor: pointer;
            background: #fff;
            border: 1px solid #eee;
        }

        .category-card:hover {
            background: #2e1a47;
            color: #fff;
        }

        /* BADGE */
        .badge-offer {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #ff3b3b;
            color: #fff;
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 5px;
        }

        /* BOTÓN HOVER */
        .card-product .btn {
            opacity: 0;
            transition: 0.3s;
        }

        .card-product:hover .btn {
            opacity: 1;
        }

        /* CATEGORÍAS PRO */
        .category-card {
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        /* ICONOS NAV */
        .nav-icons a {
            font-size: 18px;
            margin-left: 15px;
        }



        /* SLIDER PRO */
        .slider-img {
            height: 400px;
            object-fit: cover;
        }

        .carousel-caption {
            bottom: 20%;
        }

        .carousel-caption h2 {
            font-size: 40px;
        }

        .carousel-caption p {
            font-size: 18px;
        }


        .category-card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }



        /* 🔥 SLIDER CATEGORÍAS */
        .category-slider-wrapper {
            overflow: hidden;
        }

     .category-slider {
    display: flex;
    gap: 15px;
    transition: transform 0.6s ease-in-out;
    will-change: transform;
}
.category-slider-wrapper {
    overflow: hidden;
    position: relative;
}
        .category-slider::-webkit-scrollbar {
            display: none;
        }

        .category-item {
            min-width: 220px;
            flex: 0 0 auto;
        }

        /* BOTONES */
        .slider-btn {
            position: absolute;
            top: 40%;
            transform: translateY(-50%);
            background: #fff;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            z-index: 10;
        }

        .slider-btn.left {
            left: -10px;
        }

        .slider-btn.right {
            right: -10px;
        }

        .slider-btn:hover {
            background: #2e1a47;
            color: #fff;
        }


        .category-card {
            padding: 10px;
            height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* 🔥 IMAGEN PRO */
        .category-img {
            width: 100%;
            height: 90px;
            object-fit: contain;
            /* 🔥 clave (NO cover) */
            background: #f8f9fa;
            border-radius: 8px;
        }

        /* TEXTO */
        .category-card strong {
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }

        .category-card:hover img {
    transform: scale(1.05);
}

.category-img {
    transition: transform 0.3s ease;
}
    </style>

    @stack('css')
</head>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const slider = document.getElementById('categorySlider');
    const next = document.getElementById('catNext');
    const prev = document.getElementById('catPrev');

    if (!slider) return;

    const items = slider.children;
    const total = items.length;

    let index = 0;

    // 🔥 CLONAR PRIMEROS ELEMENTOS (loop real)
    slider.innerHTML += slider.innerHTML;

    function moveSlide() {
        index++;

        slider.style.transform = `translateX(-${index * 235}px)`;

        // 🔥 reset invisible
        if (index >= total) {
            setTimeout(() => {
                slider.style.transition = "none";
                index = 0;
                slider.style.transform = `translateX(0px)`;

                // 🔥 reactivar transición
                setTimeout(() => {
                    slider.style.transition = "transform 0.6s ease-in-out";
                }, 50);

            }, 600);
        }
    }

    // 🔥 autoplay tipo carousel
    let auto = setInterval(moveSlide, 2500);

    // botones
    next.addEventListener('click', () => {
        moveSlide();
    });

    prev.addEventListener('click', () => {
        if (index > 0) {
            index--;
            slider.style.transform = `translateX(-${index * 235}px)`;
        }
    });

    // pause hover
    slider.addEventListener('mouseenter', () => clearInterval(auto));
    slider.addEventListener('mouseleave', () => {
        auto = setInterval(moveSlide, 2500);
    });

});
</script>

<body>

    <!-- 🔥 NAVBAR PRO -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">

            <!-- LOGO -->
            <a class="navbar-brand" href="/">CiCo</a>

            <!-- BUSCADOR -->
            <form class="d-flex w-50 search-box">
                <input class="form-control" type="search" placeholder="Buscar productos...">
            </form>

            <!-- ACCIONES -->
            <div class="nav-icons">
                <a href="#">👤</a>
                <a href="#">🛒</a>
            </div>

        </div>
    </nav>

    <!-- CONTENIDO -->
    @yield('content')

    <!-- FOOTER -->
    <footer class="bg-dark text-white mt-5 p-4 text-center">
        © {{ date('Y') }} CiCo Ingenieros - Todos los derechos reservados
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('click', function(e) {

            if (e.target.classList.contains('add-to-cart')) {
                e.preventDefault();
                e.stopPropagation();

                let id = e.target.dataset.id;

                fetch("{{ route('cart.add') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            product_id: id
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert('Producto agregado al carrito');
                    });
            }

        });
    </script>

    @stack('js')

</body>

</html>
