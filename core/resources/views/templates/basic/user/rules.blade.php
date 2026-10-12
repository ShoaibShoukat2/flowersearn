       <?php

        $pageTitle = 'Rules';
        ?>
@extends($activeTemplate.'layouts.master')
@php
    $kycInfo = getContent('kyc_info.content',true);
@endphp

@section('content')

    <main>
        <section>
      <a href="home" class="h-44 flex items-center justify-center">
          <img src="{{ asset('assets/logoEarn.png') }}" style="width:300px;height:150px;" alt="SnapsTubes Logo">
        </a>
    <div class="px-4 pt-10 mb-10 max-w-lg mx-auto">
        <h2 class="text-4xl font-bold text-center text-white mb-8">SnapsTubes Salary</h2>
        <p class="text-white font-medium text-lg text-center">
         Salary is an extra Income which will you get after Achieving level 10. Your Salary is according to Your package. "BASIC" Daily salary is Rs 1000 and Monthly Rs 30000 "STANDARD" Daily salary is Rs 1500 and Monthly Rs 45000 "PREMIUM" Daily salary is Rs 2500 and Monthly Rs 75000 "SUPER PREMIUM" Daily salary is R's 3700 And Monthly Rs 111000.
        </p>
    </div>
</section>
    </main>

@endsection