       <?php

        $pageTitle = 'Feedback';
        ?>
@extends($activeTemplate.'layouts.master')

@section('content')
<style>
    .bg-pink {
  --tw-bg-opacity: 1;
  background-color: rgb(0 56 184 / var(--tw-bg-opacity));
}

</style>
<main>
    <section>
        <a href="home" class="h-44 flex items-center justify-center">
            <img src="{{ asset('assets/logoEarn.png') }}" style="width:300px;height:150px;" alt="SnapsTubes Logo">
        </a>
        <div class="px-4 pt-10 mb-10 max-w-lg mx-auto space-y-6">
            <h2 class="text-4xl font-bold text-center text-white">Feedback</h2>
            <p class="text-white text-xl font-medium"></p>
                @csrf
                <div class="mb-4">
                    <label for="message" class="block mb-2 text-xl font-bold text-white">Message</label>
                    <textarea name="feedback" id="message" rows="5"
                        class="bg-pink text-gray-100 text-xl rounded-lg focus:ring-0 focus:border-0 border-0 placeholder:text-gray-100 block w-full p-2.5"
                        placeholder="Enter Message..." required></textarea>
                </div>
                <span class="flex items-center justify-center">
                    <button type="submit"
                        class="text-white bg-pink hover:bg-purple-800 focus:outline-none focus:ring-4 focus:ring-purple-300 font-bold rounded-full text-xl px-5 py-2.5 text-center mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                        Submit
                    </button>
                </span>
            </form>
        </div>
    </section>
</main>


@endsection