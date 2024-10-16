<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>{{ $service->name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <style>
        input[type='number']::-webkit-inner-spin-button,
        input[type='number']::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>

<body class="bg-[#F3F6EE] relative min-h-screen overflow-x-hidden hidden" id="content">
    <div class="bg-[#E0B73E] h-64 w-64 rotate-45 absolute -top-40 -left-40"></div>
   
    <img src="https://gpglobal.b2mwap.com/assets/images/logo.png" alt="logo"
        class="absolute z-10 bottom-5 right-5 w-10">

    <div class="mx-auto relative flex justify-center">
        <div class="mt-10 sm:mt-[5rem] mx-auto justify-center items-center">
            <div class="bg-[#0C1026] flex mx-auto justify-center items-center h-14 w-14 rounded-full">
                <i class="fa-solid fa-check text-white flex justify-center items-center text-[2rem]"></i>
            </div>
            @if(!$is_success == 'subs' && $subscribers)
            <div class="mt-3 px-5">
                <h4 class="text-xl sm:text-[2rem] md:text-[3rem] leading-relaxed py-2 font-bold text-center mx-auto">Welcome To {{ $service->name }}!</h4>
                <p class="text-base font-medium text-center mx-auto">
                    We’re sorry to see you go. If you’re sure you’d like to unsubscribe, please fill out the form below
                    to proceed.
                </p>
            </div>
            @else
            <div id="unsubscribeSuccessMessage" class="text-center mt-4">
                <h4 class="text-[3rem] font-bold text-center mx-auto">You have successfully unsubscribed!</h4>
                <p class="text-base font-medium text-center mx-auto">
                    We're sorry to see you go. If you have any feedback, please let us know.
                </p>
            </div>
            @endif
            @if(!$is_success == 'subs' && $subscribers)
            <div class="mt-6  px-5 bg-white h-auto py-5 w-auto sm:w-[32rem] shadow-lg mx-auto">
                <h1 class="text-center mx-auto">
                    <span class="text-xl font-normal ">Your Subscription Package</span>
                    <p class="text-xl font-bold ">{{ $service->description }}</p>
                </h1>
                <h2 class="text-base font-semibold text-start py-2">
                    Are you sure you want to unsubscribe from this package?
                </h2>
                <h2 class="text-sm font-medium text-start mx-auto">
                    Please fill out the form below. If you are correctly fillup this form then you will show
                    "Unsubscribe Button"
                </h2>

                <div class="flex space-x-3 mt-5">
                    <div type="number" id="number_1"
                        class="peer h-10 w-full flex mx-auto justify-center items-center rounded-md bg-gray-200 px-4 font-bold outline-none drop-shadow-sm transition-all duration-200 ease-in-out focus:bg-white focus:ring-2 focus:ring-blue-400 mt-[6px]">
                    </div>
                    <span class="text-3xl flex justify-center items-center w-4 mx-auto">+</span>
                    <div type="number" id="number_2"
                        class="peer h-10 w-full flex mx-auto justify-center items-center rounded-md bg-gray-200 px-4 font-bold outline-none drop-shadow-sm transition-all duration-200 ease-in-out focus:bg-white focus:ring-2 focus:ring-blue-400 mt-[6px]">
                    </div>
                    <span class="text-3xl flex justify-center items-center w-4 mx-auto">=</span>
                    <input type="number" id="submit_number"
                        class="peer h-10 w-full rounded-md bg-gray-200 px-4 font-bold outline-none drop-shadow-sm transition-all duration-200 ease-in-out focus:bg-white focus:ring-2 focus:ring-blue-400 mt-[6px]" />
                </div>
                <div class="mt-4 mx-auto flex justify-center space-x-5">
                    <button
                        class="rounded-lg px-4 py-2 bg-teal-700 text-teal-100 hover:bg-teal-800 duration-300 btnSubmit">Submit</button>
                    <div
                        class="hidden correct_status rounded-lg px-2 py-2 bg-green-700 text-green-100 hover:bg-green-800 duration-300">
                        Correct <i class="fa-solid fa-face-smile text-white"></i>
                    </div>
                    <div
                        class="hidden wrong_status rounded-lg px-2 py-2 bg-red-700 text-red-100 hover:bg-red-800 duration-300">
                        Wrong <i class="fa-solid fa-xmark text-white"></i>
                    </div>
                </div>

                <div class="mt-4 mx-auto flex justify-center space-x-5">
                    <input type="hidden" id="acr" value="{{ $subscribers->acr }}"/>
                    <button 
                        class="hidden rounded-lg px-4 py-2 bg-red-700 text-red-100 hover:bg-red-800 duration-300 unSubsBtn">
                        Unsubscribe Now
                    </button>
                </div>

            </div>
            @endif
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Tailwind CSS has loaded, now show the content
            document.getElementById('content').classList.remove('hidden');
        });

        $(() => {
            const RANDOM_NUMBER_1 = getRandomInt(1, 20);
            const RANDOM_NUMBER_2 = getRandomInt(1, 20);
            const choiceRandomBox = getRandomInt(1, 3);
            
            // const url = window.location.href;


            $("#number_1").text(RANDOM_NUMBER_1);
            $("#number_2").text(RANDOM_NUMBER_2);
            
            $(".btnSubmit").click(() => {
                const submit_number = parseInt($('#submit_number').val());
                if ((RANDOM_NUMBER_1 + RANDOM_NUMBER_2) == submit_number) {
                    $(".correct_status").removeClass('hidden');
                    $(".wrong_status").addClass('hidden');
                    $(".unSubsBtn").removeClass('hidden');
                } else {
                    $(".correct_status").addClass('hidden');
                    $(".wrong_status").removeClass('hidden');
                    setTimeout(() => {
                        $('#submit_number').val('');
                        // $(".wrong_status").addClass('hidden');
                    }, 1500);
                }
            });


            $(".unSubsBtn").click(() => {

                const url = window.location.href;
                const urlObj = new URL(url);
                const params = new URLSearchParams(urlObj.search);

                const keyword = params.get('keyword');
                const msisdn = params.get('msisdn');
                var acr = $('#acr').val();
                const GETURL = `https://gpglobal.b2mwap.com/api/unsubscription?keyword=${keyword}&acr=${acr}&msisdn=${msisdn}`;
                axios.get(GETURL).then((res) => {
                    window.location.href = url + '&success=subs';
                });
            });
        });


        function getRandomInt(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }
    </script>
</body>

</html>
