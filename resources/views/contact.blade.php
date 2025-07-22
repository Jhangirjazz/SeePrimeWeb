@extends('Layouts.master')

@section('title','Contact US')

@section('content')
<div class="container py-5 text-white">
    
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center mb-4">
            <h1 class="text-white mt-2">Contact Us</h1>
            <p class="text-white">We'd love to hear from you. Please fill out the form below.</p>
        </div>
        <div class="col-lg-8">
            <form method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label text-white">Name</label>
                    <input type="text" class="form-control bg-dark text-white border-secondary" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label text-white">Email</label>
                    <input type="email" class="form-control bg-dark text-white border-secondary" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label text-white">Subject</label>
                    <input type="text" class="form-control bg-dark text-white border-secondary" id="subject" name="subject" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label text-white">Message</label>
                    <textarea class="form-control bg-dark text-white border-secondary" id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-danger px-4">Send Message</button>
            </form>
        </div>
    </div>
</div>
@endsection