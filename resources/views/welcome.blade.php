<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"
        integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-4 border">
                <div class="p-3">
                    <form id="create-user">
                        <div class="form-group">
                            <label>User ID</label>
                            <input type="text" name="user-id" class="form-control" id="user-id" readonly>
                        </div>
                        <div class="form-group">
                            <label>Name</label>
                            <input id="user-name" type="text" class="form-control" name="name"
                                aria-describedby="emailHelp" placeholder="User name" required>
                        </div>
                        <button id="button-create-user" type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
            <div class="right-container col-8 border d-none">
                <div class="p-3">
                    <div class="form-group w-25">
                        <label>Usuarios Online</label>
                        <select class="form-control" id="usuarios-online"></select>
                    </div>
                    <div class="chat-panel border rounded overflow-auto p-3" style="min-height: 400px">
                    </div>
                    <form id="form-send-message">
                        <input id="message" type="text" class="form-control mt-3" placeholder="Message" required>
                        <button type="submit" class="btn btn-primary mt-3">Send message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.socket.io/4.1.2/socket.io.min.js"
integrity="sha384-toS6mmwu70G0fw54EGlWWeA4z3dyJ+dlXBtSURSKN4vyRFOcxd3Bzjj/AoOwY+Rg" crossorigin="anonymous">
</script>
<script>
    const socket = io('http://127.0.0.1:3000');
    var userId = null;

    socket.emit("new-client");
    socket.on("set-client-id", (data) => {
        userId = data;
        $('#user-id').val(userId);
    });

    //Estoy usando timeout porque me tarda en llegar la respuesta del setter del userId
    setTimeout(() => {
        socket.on("reload-online-users", (data) => {
            $('#usuarios-online').empty();

            $.each(data, function(i, user) {
                if (userId != user.uid) {
                    $('#usuarios-online').append($('<option>', {
                        value: user.uid,
                        text: user.name
                    }));
                }
            });
        });
        socket.on("resive-message-" + userId, (data) => {
            $('.chat-panel').append(`<p>` + data.userName + `: ` + data.message + `</p>`);
        });
    }, 1500);

    ////////////////////////////////////////////////
    $('#create-user').submit(function(event) {
        event.preventDefault();
        $('#button-create-user').attr('disabled', true);
        $('#user-name').attr('disabled', true);
        $(".right-container").removeClass('d-none');

        const user = {
            uid: $("#user-id").val(),
            name: $("#user-name").val()
        };
        socket.emit('add-user', user);
    });

    $('#form-send-message').submit(function(event) {
        event.preventDefault();
        const message = {
            nameSender: $("#user-name").val(),
            text: $('#message').val(),
            resiverId: $('#usuarios-online :selected').val()
        };
        socket.emit('new-message', message);

        $('.chat-panel').append(`<p>Yo: ` + message.text + `</p>`);
        $('#form-send-message')[0].reset();
    })
</script>

</html>
