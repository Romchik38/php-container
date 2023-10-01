# Simple implementation of psr-11 container
1. See Description of a common interface for dependency injection containers   
[PSR-11: Container interface](https://www.php-fig.org/psr/psr-11/)

2. Clone this project  
git clone https://github.com/Romchik38/php-container.git

3. Install via composer  
composer install

## Structure
src/                        implementation  
example/                    example App using container  
console.php                 run example App in console  
index.php                   rum example App in browser  

## Run example in console
`php console.php one` should print "1"  
`php console.php some` should print "Not Found"  

## Run example in browser
1. Start http server  
psr11_folder/php -S 0.0.0.0:8000  

2. Make a request in address bar  
must return "1"  
localhost:8000/one  

must return Not found  
localhost:8000/         
localhost:8000/some  