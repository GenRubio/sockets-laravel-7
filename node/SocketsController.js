const SocketController = {
    uid: 0,
    onlineUsers: [],
    init(io) {
        this.setListeners(io);
    },
    setListeners(io) {
        io.on("connection", socket => {
            socket.clientID = SocketController.uid += 1;

            socket.on("new-client", data => {
                socket.emit("set-client-id", socket.clientID);
            });
            socket.on("add-user", data => {
                SocketController.onlineUsers.push(data);

                // io.emit( Manda mensaje a todos clientes conectados
                io.emit('reload-online-users', SocketController.onlineUsers);
            });
            socket.on("new-message", data => {
                const message = {
                    userName: data.nameSender,
                    message: data.text
                }
                io.emit('resive-message-' + data.resiverId, message);
            });

            //Canal reservado por Socket.io
            socket.on("disconnect", () => {
                let newOnlineUsers = [];
                SocketController.onlineUsers.forEach(function(user){
                    if (user.uid != socket.clientID){
                        newOnlineUsers.push(user);
                    }
                });
                SocketController.onlineUsers = newOnlineUsers;
                io.emit('reload-online-users', SocketController.onlineUsers);
            });
        });
    }
};

module.exports = SocketController;
