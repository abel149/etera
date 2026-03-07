@extends('layouts.authentication')

@section('img')
    <img src="{{ asset('assets/images/login-images/login-cover.svg') }}" class="img-fluid auth-img-cover-login" width="650" alt=""/>
@endsection

@section('content')

<div class="container py-5">
    <h2 class="mb-4 text-center">Leave a Review</h2>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(function() {
                document.querySelector('.alert-success')?.remove();
            }, 3000);
        </script>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('reviews.store') }}" method="POST">
        @csrf

        {{-- User dropdown --}}
        <div class="mb-3">
            <label for="user_id" class="form-label">Select User</label>
            <select name="user_id" id="user_id" class="form-select" required>
                <option value="">-- Select User --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Rating --}}
        <div class="mb-3">
            <label class="form-label">Rating</label>
            <div id="star-container">
                @for($i = 1; $i <= 5; $i++)
                    <span class="star" data-value="{{ $i }}" style="font-size: 1.5rem; cursor: pointer;">&#9733;</span>
                @endfor
            </div>
            <input type="hidden" name="rating" id="rating" required>
        </div>

        {{-- Review text --}}
        <div class="mb-3">
            <label for="review" class="form-label">Review</label>
            <textarea name="review" id="review" class="form-control" rows="4" placeholder="Write your review..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>
</div>

<script>
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const value = star.dataset.value;
            ratingInput.value = value;

            stars.forEach(s => s.classList.remove('selected'));
            for (let i = 0; i < value; i++) {
                stars[i].classList.add('selected');
            }
        });
    });
</script>

<style>
    .star.selected {
        color: #ffc107; /* Highlighted star color */
    }
</style>

@endsection
