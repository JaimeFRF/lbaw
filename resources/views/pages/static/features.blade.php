@extends('layouts.app')

@section('css')
<link href="{{ url('css/about.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('partials.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs , 'current' => $current ])

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center mb-4">Main Features</h2>

                <div class="card-deck">
                    <div class="card">
                        <div class="card-body">
                            <div class="title-content" onclick="toggleDescription(this)">
                                <h5 class="card-title">Versatile Navigation Menu</h5>
                                <span class="arrow">&#9660;</span>
                            </div>
                            <p class="card-text description">Our website includes a simple but effective navigational menu allowing users to easily navigate the shop.</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="title-content" onclick="toggleDescription(this)">
                                <h5 class="card-title">Filtering and Sorting of Products</h5>
                                <span class="arrow">&#9660;</span>
                            </div>
                            <p class="card-text description">Our website empowers customers to easily filter and sort products, catering to their specific preferences. This user-friendly feature ensures a personalized experience, displaying only the products that meet the customer's desires. </p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="title-content" onclick="toggleDescription(this)">
                                <h5 class="card-title">Shopping Cart and Wishlist</h5>
                                <span class="arrow">&#9660;</span>
                            </div>
                            <p class="card-text description">Our website allows users to effortlessly add items to their virtual shopping cart, as well as view and manage the contents of your cart. Additionally, it lets users create a personalized wishlist, where you have the freedom to manage your most coveted items.</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="title-content" onclick="toggleDescription(this)">
                                <h5 class="card-title">Guest Shopping Cart</h5>
                                <span class="arrow">&#9660;</span>
                            </div>
                            <p class="card-text description">A guest still has the possibility to add items to a shopping cart </p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="title-content" onclick="toggleDescription(this)">
                                <h5 class="card-title">Product Reviews</h5>
                                <span class="arrow">&#9660;</span>
                            </div>
                            <p class="card-text description">After purchasing a product, users have the ability to share their thoughts by providing a brief commentary and assigning a rating on a scale from 0 to 5 stars.</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="title-content" onclick="toggleDescription(this)">
                                <h5 class="card-title">Account Managing when Authenticated</h5>
                                <span class="arrow">&#9660;</span>
                            </div>
                            <p class="card-text description">Our website allows authenticated users to fully manage their account granting the possibility to fully change their information such as name, username, password and profile picture. The user is also capable of deleting their own account.</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="title-content" onclick="toggleDescription(this)">
                                <h5 class="card-title">Order Management</h5>
                                <span class="arrow">&#9660;</span>
                            </div>
                            <p class="card-text description">After making an order, the user is fully capable of canceling said order through their profile.</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="title-content" onclick="toggleDescription(this)">
                                <h5 class="card-title">Admin Actions</h5>
                                <span class="arrow">&#9660;</span>
                            </div>
                            <p class="card-text description">As an admin, there are several possible actions, such as adding/editing items, adding/editing user accounts and creating admin accounts and also changing the status of orders made.</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="title-content" onclick="toggleDescription(this)">
                                <h5 class="card-title">Real-Time Notifications</h5>
                                <span class="arrow">&#9660;</span>
                            </div>
                            <p class="card-text description">Our website supports several real-time notifications such as item restocking or order status change. The notifications are sent to the respective user as soons as the status of the order is changed or an item in their wishlist is restocked.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleDescription(element) {
        const description = element.nextElementSibling;
        const arrow = element.querySelector('.arrow');

        if (description.style.display === 'none' || description.style.display === '') {
            description.style.display = 'block';
            arrow.innerHTML = '&#9650;'; // Change the arrow to up
        } else {
            description.style.display = 'none';
            arrow.innerHTML = '&#9660;'; // Change the arrow to down
        }
    }

    document.querySelectorAll('.description').forEach(description => {
        description.style.display = 'none';
    });
</script>

    
@endsection
