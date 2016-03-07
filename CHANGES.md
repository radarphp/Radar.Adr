First beta release.

- Extract new package, "Arbiter", and use it for action handling.

- Add a parameter to set a custom "routing failed" responder class in the RoutingHandler.

- Use "401" response code for Responder::notAuthenticated().

- Use PayloadStatus from Payload_Interface, instead of embedded payload statuses.

- Added option to use autoresolving container at boot time.
