<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    * {
    padding: 0;
    margin: 0
}

.wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
}

.animated-check {
    height: 10em;
    width: 10em
}

.animated-check path {
    fill: none;
    stroke: #7ac142;
    stroke-width: 4;
    stroke-dasharray: 23;
    stroke-dashoffset: 23;
    animation: draw 1s linear forwards;
    stroke-linecap: round;
    stroke-linejoin: round
}

@keyframes draw {
    to {
        stroke-dashoffset: 0
    }
}
  </style>
</head>
<body class="bg-[#F1F9FF] w-full mx-auto">
  
  <div class="text-center mt-[2rem]">
    <img class="mx-auto h-[9rem] w-[40rem] text-center" src="{{asset('assets/images/gp_consent.jpg')}}" alt="gp consent image"/>
  </div>
  <div class="text-center mt-[2rem]">
    <h1 class="text-2xl font-semibold text-stone-600">Consent Successful</h1>
    <div class="wrapper"> <svg class="animated-check" viewBox="0 0 24 24">
      <path d="M4.1 12.7L9 17.6 20.3 6.3" fill="none" /> </svg>
      </div>
    </div>
    <div class="text-center py-3">
      <h3 class="text-center text-xl font-medium text-stone-600">Thank you for your consent. You can now proceed to the next step.</h3>
    </div>
</body>
</html>