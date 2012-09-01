phpPing
=======

Simple class using PHP to tell you whether a device is pingable or not.

Usage
-----
Pretty darned simple.  Just call the send method passing the name or IP address of the device to ping.

    ping::send('server1');

or

    ping::send('192.168.100.43');

Either true or false will be returned to indicate whether the device is pingable.

How it works
-----
Calling the send method will check to see whether the sockets extension is enabled.  If it is a socket connection will attempt to be made and the response returned.

Note that a socket connection requires root access to be created.  If running under Windows, this means that the Application Pool must be running under an admin account.

If you attempt to ping a device by name, the web server must be able to resolve that name.

If the sockets extension is disabled, or the socket connection fails for any reason, the class will fall back to attempting to ping the device by running exec.  The input is escaped before running exec for security reasons.

Note that the current ping command when running via exec is specific to Windows.

Requirements
-----
You'll need my [array class](https://github.com/scoobydoobydoo/arrayHelper) in order for the exec method to return a true or false result.  This is because it uses the array class to search for a partial string within an array value.

Alternatively you can just include the following function

    function arraySearch($array, $searchTerm) {
        foreach ($array as $value) {
            $value = strtolower($value);
            $searchTerm = strtolower($searchTerm);
            if (strpos($value, $searchTerm)) {
                return true;
            }
        }
        
        return false;
    }

and then modify the following line in the execSend method

    return arr::search($result, 'received = 1') ? true : false;

to

    return arraySearch($result, 'received = 1') ? true : false;
