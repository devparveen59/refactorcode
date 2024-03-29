Do at least ONE of the following tasks: refactor is mandatory. Write tests is optional, will be good bonus to see it. 
Upload your results to a Github repo, for easier sharing and reviewing.

Thank you and good luck!



Code to refactor
=================
1) app/Http/Controllers/BookingController.php
2) app/Repository/BookingRepository.php

Code to write tests (optional)
=====================
3) App/Helpers/TeHelper.php method willExpireAt
4) App/Repository/UserRepository.php, method createOrUpdate


----------------------------

What I expect in your repo:

X. A readme with:   Your thoughts about the code. What makes it amazing code. Or what makes it ok code. Or what makes it terrible code. How would you have done it. Thoughts on formatting, structure, logic.. The more details that you can provide about the code (what's terrible about it or/and what is good about it) the easier for us to assess your coding style, mentality etc

And 

Y.  Refactor it if you feel it needs refactoring. The more love you put into it. The easier for us to asses your thoughts, code principles etc


IMPORTANT: Make two commits. First commit with original code. Second with your refactor so we can easily trace changes. 


NB: you do not need to set up the code on local and make the web app run. It will not run as its not a complete web app. This is purely to assess you thoughts about code, formatting, logic etc


===== So expected output is a GitHub link with either =====

1. Readme described above (point X above) + refactored code 
OR
2. Readme described above (point X above) + refactored core + a unit test of the code that we have sent

Thank you!


==============================Parveen's Observations========================================================

Hi,

I have checked and refactored the code. Here are my observations and modifications:

Code Duplication:
I removed multiple duplicated lines of code by extracting common functionality into separate blocks. This makes the code cleaner and easier to maintain.

Code Reusability:
I structured the code into reusable blocks, each responsible for a specific task. This improves modularity and makes it easier to extend or modify functionality in the future.

Keeping Sensitive Data Safe:
I implemented private functions to access user roles, ensuring that sensitive information is securely handled and not exposed unnecessarily.

Divide Code into Smaller Parts:
To improve readability and manageability, I divided the code into smaller, more focused chunks. This makes it easier to understand and maintain.

Removed Unused Variables and Queries:
I eliminated unused variables and redundant database queries to optimize memory usage and improve performance.

Logic Adjustments:
I made adjustments to the logic of the code where necessary to enhance efficiency and clarity.

Removed Unwanted Namespaces:
I removed unnecessary namespaces to declutter the code and improve readability.

Note: I added status codes as responses in the current files. However, I'm unsure about the specific response format used by the views or the requester who calls these functions. We may need to adjust this based on the requirements of the consuming end.

Thanks
Parveen