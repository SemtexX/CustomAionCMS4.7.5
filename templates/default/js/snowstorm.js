var snowStorm = (function(window, document) {
    var flakes = [];
    var screenX, screenY;
    var animationFrame;

    function SnowFlake(x, y, type) {
        this.x = x;
        this.y = y;
        this.type = type;
        this.vX = Math.random() * 2 - 1; // Random X velocity
        this.vY = Math.random() * 2 + 1; // Random Y velocity
        this.width = Math.floor(Math.random() * 10) + 5; // Random width between 5 and 15
        this.height = this.width; // Set height same as width for simplicity
        this.active = true;
    }

    function createSnow() {
        for (var i = 0; i < 100; i++) { // Adjust the number of flakes as needed
            var x = Math.random() * screenX;
            var y = Math.random() * screenY;
            var type = Math.floor(Math.random() * 6); // Random type
            flakes.push(new SnowFlake(x, y, type));
        }
    }

    function moveSnow() {
        for (var i = 0; i < flakes.length; i++) {
            var flake = flakes[i];
            flake.x += flake.vX;
            flake.y += flake.vY;

            // Recycle flakes that go off screen
            if (flake.y > screenY) {
                flake.y = 0;
                flake.x = Math.random() * screenX;
            }
        }
    }

    function drawSnow() {
        // Clear previous frame
        // Draw flakes
        for (var i = 0; i < flakes.length; i++) {
            var flake = flakes[i];
            // Draw each flake with size based on flake.width and flake.height variables
        }
    }

    function animate() {
        moveSnow();
        drawSnow();
        animationFrame = requestAnimationFrame(animate);
    }

    function start() {
        // Set up screen dimensions
        screenX = window.innerWidth;
        screenY = window.innerHeight;

        createSnow();
        animate();
    }

    function stop() {
        cancelAnimationFrame(animationFrame);
    }

    return {
        start: start,
        stop: stop
    };

}(window, document));

snowStorm.start();
