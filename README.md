# php-chat-application
Php chat application (under development)

Web chat application with PHP, MySQL, Javascript, jQuery, bootstrap, HTML

Architecture
  * REST based web api with JSON requests and MVC architecture
  * Native PHP coded core server modules: Data Access Object, authentication, session handling, logging functionality
  * Single Page Application with client side page rendering and DOM management with jQuery, bootstrap and javascript
  * Loosely coupled classes, reusable code



Business functionalities
  * Registration, authentication
  * View and edit personal profile
  * List registered users and view their profile
  * Send message to registered users
  * View sent and received messages
  * Delete personal data



Security efforts
  * Sql injection protection with php prepared statements
  * Low privileged mysql account
  * Minimalist error messages
  * SHA512 password hashes



Future plans
  * CSRF and XSS protection with CSRF tokens and HTTP Security headers
  * Login bruteforce protection with IP blacklisting and username banning
  * More secure hashing for passwords like bcrypt
  * Logging of suspicious user acitivity
  * Using secure random value generators
