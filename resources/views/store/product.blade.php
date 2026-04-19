@extends('store.layouts.app')

@section('content')

<div class="container my-5">

    <div class="row">

        <!-- 🔥 GALERÍA PRO -->
        <div class="col-md-6 d-flex">

            <!-- MINIATURAS VERTICALES -->
            <div class="me-3 d-flex flex-column gap-2">
                @forelse ($product->images as $image)
                    <img src="{{ asset('storage/' . $image->image) }}"
                        class="img-thumbnail thumb-img"
                        style="width: 70px; height: 70px; object-fit: cover; cursor:pointer;">
                @empty
                    <img src="https://via.placeholder.com/70" class="img-thumbnail">
                @endforelse
            </div>

            <!-- IMAGEN PRINCIPAL -->
            <div class="flex-grow-1">
                <img id="mainImage"
                    src="{{ $product->images->first() 
                        ? asset('storage/' . $product->images->first()->image) 
                        : 'https://via.placeholder.com/500x400' }}"
                    class="img-fluid rounded shadow w-100 main-img">
            </div>

        </div>

        <!-- 🔥 INFO TIPO ECOMMERCE -->
        <div class="col-md-6">

            <h4 class="fw-bold mb-2">{{ $product->name }}</h4>

            <p class="text-muted mb-2">
                {{ $product->short_description }}
            </p>

            <!-- PRECIO -->
            <h2 class="price mb-3">
                S/ {{ number_format($product->price, 2) }}
            </h2>

            <!-- CANTIDAD -->
            <div class="d-flex align-items-center mb-3">
                <button class="btn btn-outline-secondary btn-sm">-</button>
                <input type="text" value="1" class="form-control text-center mx-2" style="width:60px;">
                <button class="btn btn-outline-secondary btn-sm">+</button>
            </div>

            <!-- BOTÓN -->
            <button class="btn btn-primary btn-lg w-100 mb-3 add-to-cart"
                data-id="{{ $product->id }}">
                🛒 Agregar al carrito
            </button>

            <!-- INFO EXTRA -->
            <ul class="list-unstyled small text-muted">
                <li>✔ Producto disponible</li>
                <li>✔ Entrega rápida</li>
                <li>✔ Garantía incluida</li>
            </ul>

            <hr>

            <!-- DESCRIPCIÓN -->
            <div>
                {!! $product->description !!}
            </div>

        </div>

    </div>

</div>

@endsection

@push('css')
<style>
    .thumb-img {
        transition: 0.3s;
    }

    .thumb-img:hover {
        border: 2px solid #00c897;
        transform: scale(1.05);
    }

    .main-img {
        max-height: 450px;
        object-fit: contain;
    }
</style>
@endpush

@push('js')
<script>
    // CAMBIO DE IMAGEN
    document.querySelectorAll('.thumb-img').forEach(img => {
        img.addEventListener('click', function () {
            document.getElementById('mainImage').src = this.src;
        });
    });
</script>
@endpush