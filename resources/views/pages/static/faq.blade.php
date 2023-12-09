@extends('layouts.app')

@section('css')
    <link href="{{ url('css/faq.css') }}" rel="stylesheet">
@endsection

@section('content')
@include('partials.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs , 'current' => $current ])

<body>
    
    <h6>F.A.Q</h6>

    <div class="faq-section">
        <div class="question" onclick="toggleAnswer(this)">
            <span class="arrow">&#9660;</span> Q: How long do orders take to be shipped ?
        </div>
        <div class="answer">A: Orders usually take around 1-2 business days to be shipped excluding holidays. Any orders placed during the weekend will only be handled on the next Monday.</div>
    </div>

    <div class="faq-section">
        <div class="question" onclick="toggleAnswer(this)">
            <span class="arrow">&#9660;</span> Q: How long does it take for an order to arrive at my place ?
        </div>
        <div class="answer">A: The delivering process is passed on to the delivering company and it's up to them to determine the delivery date. It should take no longer than 7 business days.</div>
    </div>

    <div class="faq-section">
        <div class="question" onclick="toggleAnswer(this)">
            <span class="arrow">&#9660;</span> Q: How can I track my order ?
        </div>
        <div class="answer">A: Once your order is shipped, you'll receive a confirmation email with a tracking number. You can use this number to track your order's delivery status on our website or the courier's tracking portal.</div>
    </div>

    <div class="faq-section">
        <div class="question" onclick="toggleAnswer(this)">
            <span class="arrow">&#9660;</span> Q: What payment methods do you accept ?
        </div>
        <div class="answer">A: We accept a variety of payment methods, including credit/debit cards and online payment services. At checkout, you'll see the available options to choose from.</div>
    </div>

    <div class="faq-section">
        <div class="question" onclick="toggleAnswer(this)">
            <span class="arrow">&#9660;</span> Q: What is the return policy ?
        </div>
        <div class="answer">A: Our return policy allows you to return items within a 30-day period after receiving your order. We accept returns for items that are unused, in their original packaging, and in resalable condition. There may be a return fee.</div>
    </div>

    <script>
        function toggleAnswer(element) {
            const answer = element.nextElementSibling;
            const arrow = element.querySelector('.arrow');

            if (answer.style.display === 'none' || answer.style.display === '') {
                answer.style.display = 'block';
                arrow.classList.add('up');
            } else {
                answer.style.display = 'none';
                arrow.classList.remove('up');
            }
        }

        document.querySelectorAll('.answer').forEach(answer => {
            answer.style.display = 'none';
        });
    </script>
</body>
@endsection