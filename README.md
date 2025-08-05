**To get this to work with MAMP PRO and apache**  
I had to use the symfony/apache-pack.  
It may ask you to run the script as part of the vendor install.  
I chose option "p" (permanent)  

**To Create the JWT Token stuff for the API**  
mkdir -p config/jwt  
openssl genrsa -out config/jwt/private.pem -aes256 4096  
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem  

chmod 600 config/jwt/private.pem  

**Copy the .env.example file to .env**  
In the .env file, put the PassPhrase you used to create the *.pem files  
###> lexik/jwt-authentication-bundle ###  
JWT_PASSPHRASE=your_passphrase_used_above  
###< lexik/jwt-authentication-bundle ###  
