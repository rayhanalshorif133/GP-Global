<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
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
    stroke: #b82c2c;
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
    <h1 class="text-2xl font-semibold text-stone-600">
        Requested Error
    </h1>
    <div class="wrapper py-5"> 
         <i class="fa-solid fa-circle-info text-[7rem] text-blue-500"></i>
    </div>
    </div>
    <div class="text-center py-3">
      <h3 class="text-center text-xl font-medium text-stone-600">
        Please try again later, please contact with us if you have any questions.
      </h3>
    </div>
</body>
</html>