@extends('store.layouts.app')

@section('content')
    <!-- 🔥 HERO (SE QUEDA) -->
    <!-- 🔥 SLIDER TIPO FALABELLA -->
    <div id="mainSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">

        <!-- INDICADORES -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="2"></button>
        </div>

        <!-- SLIDES -->
        <div class="carousel-inner">

            <div class="carousel-item active">
                <img src="https://plus.unsplash.com/premium_photo-1713110640802-28de8804e163?q=80&w=870&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                    class="d-block w-100 slider-img">

                <div class="carousel-caption text-start">
                    <h2 class="fw-bold">Impulsa tu negocio</h2>
                    <p>Sistemas y soluciones digitales</p>
                    <a href="#" class="btn btn-light">Ver productos</a>
                </div>
            </div>

            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1569317002804-ab77bcf1bce4?q=80&w=870&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                    class="d-block w-100 slider-img">

                <div class="carousel-caption text-start">
                    <h2 class="fw-bold">Sistemas PRO</h2>
                    <p>Automatiza y escala tu empresa</p>
                    <a href="#" class="btn btn-dark">Comprar</a>
                </div>
            </div>

            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1604940500627-d3f44d1d21c6?q=80&w=870&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                    class="d-block w-100 slider-img">

                <div class="carousel-caption text-start">
                    <h2 class="fw-bold">Servicios</h2>
                    <p>Soporte y desarrollo a medida</p>
                    <a href="#" class="btn btn-success">Solicitar</a>
                </div>
            </div>

        </div>

        <!-- FLECHAS -->
        <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>

    </div>

    <!-- 🔥 CATEGORÍAS PRO -->
    <section class="container my-5">
        <h3 class="mb-4">Explora categorías</h3>
        <div class="category-slider-wrapper position-relative">

            <!-- BOTÓN IZQUIERDA -->
            <button class="slider-btn left" id="catPrev">‹</button>

            <!-- SLIDER -->
            <div class="category-slider" id="categorySlider">

                @forelse ($categories as $category)
                    <div class="category-item">

                        <div class="card category-card p-2 text-center">

                            <img src="{{ $category->images->first()
                                ? asset('storage/' . $category->images->first()->image)
                                : 'https://us.123rf.com/450wm/koblizeek/koblizeek2208/koblizeek220800128/190320173-no-image-vector-symbol-missing-available-icon-no-gallery-for-this-moment-placeholder.jpg' }}"
                                class="category-img mb-2">

                            <strong>{{ $category->name }}</strong>

                        </div>

                    </div>
                @empty
                    <p>No hay categorías</p>
                @endforelse

            </div>

            <!-- BOTÓN DERECHA -->
            <button class="slider-btn right" id="catNext">›</button>

        </div>
    </section>

    <!-- 🔥 OFERTAS -->
    <section class="container my-5">
        <h3 class="mb-4">🔥 Ofertas</h3>

        <div class="row">
            <div class="row">

                @forelse ($products as $product)
                    <div class="col-md-3 mb-4">
                        <a href="{{ route('store.product', $product->slug) }}" class="text-decoration-none text-dark">

                            <div class="card card-product">


                                {{-- IMAGEN --}}
                                <img
                                    src="{{ $product->images->first()
                                        ? asset('storage/' . $product->images->first()->image)
                                        : 'https://via.placeholder.com/300x200' }}">

                                <div class="card-body">
                                    <h6>{{ $product->name }}</h6>

                                    <p class="price">S/ {{ number_format($product->price, 2) }}</p>

                                    <button class="btn btn-primary w-100">
                                        Agregar al carrito
                                    </button>
                                </div>

                            </div>
                        </a>
                    </div>
                @empty
                    <p>No hay productos disponibles</p>
                @endforelse

            </div>

        </div>
    </section>

    <!-- 🔥 PRODUCTOS -->
    <section class="container my-5">
        <h3 class="mb-4">Productos destacados</h3>

        <div class="row">

            @for ($i = 1; $i <= 8; $i++)
                <div class="col-md-3 mb-4">
                    <div class="card card-product">

                        <img src="https://via.placeholder.com/300x200">

                        <div class="card-body">
                            <h6>Producto {{ $i }}</h6>
                            <p class="price">S/ {{ rand(100, 900) }}</p>

                            <button class="btn btn-primary w-100">
                                Agregar al carrito
                            </button>
                        </div>

                    </div>
                </div>
            @endfor

        </div>
    </section>
@endsection
