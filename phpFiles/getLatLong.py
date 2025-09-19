from geopy.geocoders import Nominatim
import sys
'''
Get latitude and longitude from the name of a city with geopy
TODO: Should probably add some error handling even though it should
never be called incorrectly from getWeather.php
'''
if len(sys.argv) > 1:
    city_name = sys.argv[1]

    geolocator = Nominatim(user_agent="getLatLong")

    #city_name = "Binghamton NY"
    location = geolocator.geocode(city_name)
#print(location)

    if location:
        lat = location.latitude
        long = location.longitude

        lat = str(lat)
        long = str(long)

        if lat[0] == '-':
            lat = lat[:6]
        else:
            lat = lat[:5]

        if long[0] == '-':
            long = long[:6]
        else:
            long = long[:5]


       # lat = str(lat)[:5]
       # long = str(long)[:5]
        print(f"{lat},{long}")

    else:
        print("Could not find location")
