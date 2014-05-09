var app = require('http').createServer(handler)
var io = require('socket.io').listen(app)
var fs = require('fs')
var port = 1337;

process.on('uncaughtException', function (err) {
  console.error(err);
  console.log("Node NOT Exiting...");
});

app.listen(port);

function handler (req, res) {
  fs.readFile(__dirname + '/desktop.html',
  function (err, data) {
    if (err) {
      res.writeHead(500);
      return res.end('Error loading desktop.html');
    }
    res.writeHead(200, {'Content-Type': 'text/html'});
    res.end(data);
  });
}

//io.set('log level', 1);

var desktops = [];
var mobiles = [];

var addDesktop = function (socket, data) {
    var desktop = {
        socket: socket,
        session: data.session,
        ident: data.ident,
        listening: null,
        remotekey: Math.floor(Math.random() * 9000) + 1000
    }
    desktops.push(desktop);
    return desktop;
}
var addMobile = function (socket, data) {
    var mobile = {
        socket: socket,
        session: data.session
    }
    mobiles.push(mobile);
    return mobile;
}

var getDesktopSession = function (mobile) {
    for(var i=0; i<desktops.length; i++) {
        if(mobile.session === desktops[i].session) {
            return desktops[i];
        }
    }
    return;
}

var getMobileSessions = function (desktop) {
    var ret_mobiles = [];
    for(var i=0; i<mobiles.length; i++) {
        if(desktop.session === mobiles[i].session) {
            ret_mobiles.push(mobiles[i]);
        }
    }
    return ret_mobiles;
}

var getSessions = function (type) {
    var sessions = [];
    for(var i=0; i < desktops.length; i++) {
        if(type == 0)
            sessions.push(desktops[i].session);
        else if(type == 1)
            sessions.push(desktops[i].ident);
        else if(type == 2)
            sessions.push(desktops[i].listening);
    }
    return sessions;
}

var removeDesktop = function(desktop) {
    for(var i=0; i<desktops.length; i++) {
        if(desktop.session === desktops[i].session) {
            var mobiles = getMobileSessions(desktops[i]);
            for(var i = 0; i < mobiles.length; i++)
                mobiles[i].socket.emit('disconnected');

            desktops.splice(i, 1);
            return;
        }
    }
}

var removeMobile = function(mobile) {
    for(var i=0; i<mobiles.length; i++) {
        if(mobile.session === mobiles[i].session) {
            mobiles.splice(i, 1);
            return;
        }
    }
}

var refresh = function (mobile) {
    if(getDesktopSession(mobile) && getDesktopSession(mobile).listening != null)
        mobile.socket.emit('listening', {title: getDesktopSession(mobile).listening});

    mobile.socket.emit('sessions', { list_session: getSessions(0), list_ident: getSessions(1), list_listening: getSessions(2) });

}

io.sockets.on('connection', function (socket) {

    socket.on('join-server', function (data) {
        var desktop = addDesktop(socket, data);

        socket.on('listening', function (data) {
            desktop.listening = data.title;
            var mobiles = getMobileSessions(desktop);
            for(var i=0; i < mobiles.length; i++)
                mobiles[i].socket.emit('listening', data);
        });

        socket.on('get-remote-key', function () {
            desktop.socket.emit('get-remote-key', desktop.remotekey);
        });

        socket.on('disconnect', function () {
            removeDesktop(desktop);
        });
    });

    socket.on('join-mobile', function (data) {
        var mobile = addMobile(socket, data);

        refresh(mobile);

        socket.on('check-code', function(data) {
            var desktop = getDesktopSession(mobile);
            if(desktop.remotekey == data.code)
                mobile.socket.emit('check-code', true);
            else
                mobile.socket.emit('check-code', false);
        })

        socket.on('next', function () {
            var desktop = getDesktopSession(mobile);
            if(desktop) desktop.socket.emit('next');
        });

        socket.on('previous', function () {
            var desktop = getDesktopSession(mobile);
            if(desktop) desktop.socket.emit('previous');
        });

        socket.on('pause', function () {
            var desktop = getDesktopSession(mobile);
            if(desktop) desktop.socket.emit('pause');
        });

        socket.on('refresh', function () {
            refresh(mobile);
        });

        socket.on('disconnect', function () {
            removeMobile(mobile);
        });
    });
});
