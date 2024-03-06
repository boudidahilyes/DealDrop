
if(deliveryManId !== -1)
{
  function sendLocation() {

      navigator.geolocation.getCurrentPosition(
          (position) => {
              $.ajax({
                method: "POST",
                url: "/update_deliveries_location",
                data:{
                  type: 'location',
                  location: position.coords.latitude+ ',' +position.coords.longitude,
                  id: deliveryManId
              }
            });
          },
          (error) => {
              console.error('Error getting geolocation:', error.message);
          }
      );
  }
  var interval = setInterval(sendLocation, 1000);

};


