<!-- GETTING STARTED -->
## Installation
git clone https://github.com/michaelDvoretskiy/testSymfony.git
composer install
set correct values for DATABASE_URL, SUBDIR, and SOURCE_API_URL (SUBDIR leave empty for the most cases)
symfony console d:s:u --force
run project using web-server or cli command "symfony server:start"
visit /lista URL

<!-- ROUTES AND COMMANDS -->
## Routes
/register - register form to add new user
/login - login form
/lista - UI for list of posts
/posts - API for list of posts (return json)

## Commands
"symfony console posts:get" - downloading users and posts

<!-- ADDITIONAL COMMENT -->
## Additional comment
There were some adjustments made in code to make possible it work on the hosting. These changes are on FOR-HOSTING branch of the repository

<!-- CONTACT -->
## Contact
Mykhailo Dvoretskyi - m.dvoretskiy@gmail.com