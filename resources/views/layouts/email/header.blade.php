<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="icon" href="{{ asset('img/icons/icon-72x72.png') }}">
<title>Notification</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    * {
        font-family: 'QuickSand', sans-serif;
        padding: 0;
        margin: 0;
    }

    .header {
        background-color: #DF6951 !important;
        color: #FFF;
        padding: 4rem;
        text-align: center;
        font-weight: bold;
        font-size: 30px;
    }

    .body {
        background-color: #FFF !important;
        color: #000;
        padding: 2rem;
    }

    .footer {
        background-color: #f1f5f0 !important;
        color: #000;
        padding: 2rem 4rem;
        text-align: center;
        font-weight: bold;
        font-size: 16px;
    }

    .footer a {
        color: #000;
        text-decoration: none;
    }

    /* row col bootstrap */
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }

    .col-1 {
        flex: 0 0 8.333333%;
        max-width: 8.333333%;
    }

    .col-2 {
        flex: 0 0 16.666667%;
        max-width: 16.666667%;
    }

    .col-3 {
        flex: 0 0 25%;
        max-width: 25%;
    }

    .col-4 {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
    }

    .col-5 {
        flex: 0 0 41.666667%;
        max-width: 41.666667%;
    }

    .col-6 {
        flex: 0 0 50%;
        max-width: 50%;
    }

    .col-7 {
        flex: 0 0 58.333333%;
        max-width: 58.333333%;
    }

    .col-8 {
        flex: 0 0 66.666667%;
        max-width: 66.666667%;
    }

    .col-9 {
        flex: 0 0 75%;
        max-width: 75%;
    }

    .col-10 {
        flex: 0 0 83.333333%;
        max-width: 83.333333%;
    }

    .col-11 {
        flex: 0 0 91.666667%;
        max-width: 91.666667%;
    }

    .col-12 {
        flex: 0 0 100%;
        max-width: 100%;
    }

    .mb-3 {
        margin-bottom: 1rem !important;
    }

    .mb-4 {
        margin-bottom: 2rem !important;
    }

    .mt-3 {
        margin-top: 1rem !important;
    }

    .mt-4 {
        margin-top: 2rem !important;
    }

    .my-3 {
        margin-top: 1rem !important;
        margin-bottom: 1rem !important;
    }

    .my-4 {
        margin-top: 2rem !important;
        margin-bottom: 2rem !important;
    }

    .fw-bold {
        font-weight: bold !important;
    }

    .fw-light {
        font-weight: 300 !important;
    }

    .btn {
        display: inline-block;
        font-weight: 400;
        color: #212529;
        text-align: center;
        vertical-align: middle;
        user-select: none;
        background-color: transparent;
        border: 1px solid transparent;
        padding: .375rem .75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: .25rem;
        transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }

    .btn-sm {
        padding: .25rem .5rem;
        font-size: .875rem;
        line-height: 1.5;
        border-radius: .2rem;
    }

    .btn-success {
        color: #fff;
        background-color: #DF6951;
        border-color: #DF6951;
    }

    .d-grid {
        display: grid;
    }

    .text-decoration-none {
        text-decoration: none !important;
    }
</style>
