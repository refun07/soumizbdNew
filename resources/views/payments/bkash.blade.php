<!DOCTYPE html>
<html>

<head>
    <title>Pay with Bkash</title>
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, user-scalable=no" />

    <!-- SCRIPTS -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>

    @if (get_setting('bkash_sandbox') == 1)
        <script src="https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js"></script>
    @else
        <script src="https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js"></script>
    @endif

    <style>
        .text-center {
            text-align: center;
        }

        .container {
            max-width: 1250px !important;
            margin: 0 auto;
        }

        .hidden {
            visibility: hidden;
        }
    </style>
</head>

<body class="text-center">
    <div class="container">
        <button id="bKash_button" class="hidden">Pay With bKash</button>
    </div>
</body>

</html>

<script type="text/javascript">
    $(document).ready(function() {
        $('#bKash_button').trigger('click');
    });

    var paymentID = '';
    bKash.init({
        paymentMode: 'checkout', //fixed value ‘checkout’
        //paymentRequest format: {amount: AMOUNT, intent: INTENT}
        //intent options
        //1) ‘sale’ – immediate transaction (2 API calls)
        //2) ‘authorization’ – deferred transaction (3 API calls)
        paymentRequest: {
            amount: '{{ $amount }}', //max two decimal points allowed
            intent: 'sale'
        },
        createRequest: function(
            request
        ) { //request object is basically the paymentRequest object, automatically pushed by the script in createRequest method
            $.ajax({
                url: '{{ route('bkash.checkout', ['token' => $token, 'amount' => $amount]) }}',
                type: 'POST',
                contentType: 'application/json',
                success: function(data) {
                    //console.log(data);
                    data = JSON.parse(data);
                    if (data && data.paymentID != null) {
                        paymentID = data.paymentID;
                        bKash.create().onSuccess(
                            data
                        ); //pass the whole response data in bKash.create().onSucess() method as a parameter
                    } else {
                        alert(data.errorMessage);
                        bKash.create().onError();
                    }
                },
                error: function() {
                    bKash.create().onError();
                }
            });
        },
        executeRequestOnAuthorization: function() {
            $.ajax({
                url: '{{ route('bkash.execute', $token) }}',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    "paymentID": paymentID
                }),
                success: function(data) {
                    //console.log(data);
                    var result = JSON.parse(data);
                    if (result && result.paymentID != null) {
                        window.location.href = "{{ route('bkash.success') }}?payment_details=" +
                            data; //Merchant’s success page
                    } else {
                        alert(result.errorMessage);
                        bKash.execute().onError();
                    }
                },
                error: function() {
                    bKash.execute().onError();
                }
            });
        },
        onClose: function() {
            alert("Payment cancelled");
            window.location.href = "{{ route('home') }}";
        }
    });
</script>
