
if(deliveryManId !== -1)
{
  const socket = new WebSocket('ws://127.0.0.10:8000'); // Change the WebSocket server URL/port

        // Function to send location updates
        function sendLocation() {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    socket.send(JSON.stringify({
                        type: 'location',
                        location: position.coords.latitude+ ',' +position.coords.longitude,
                        id: deliveryManId
                    }));
                },
                (error) => {
                    console.error('Error getting geolocation:', error.message);
                }
            );
        }
        // Send location updates every 5 seconds
        setInterval(() => {
            sendLocation();
        }, 1000);
}
