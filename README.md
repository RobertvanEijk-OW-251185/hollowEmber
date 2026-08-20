<!-- -->

<!-- # hollowEmber -->

<!-- REPLACE ALL THE [USERNAME] TEXT WITH YOUR GITHUB PROFILE NAME & THE [PROJECTNAME] WITH THE NAME OF YOUR GITHUB PROJECT -->

<!-- Repository Information & Links-->
<br />

![GitHub repo size](https://img.shields.io/github/repo-size/RobertvanEijk-OW-251185/hollowEmber?color=000000)
![GitHub watchers](https://img.shields.io/github/watchers/RobertvanEijk-OW-251185/hollowEmber?color=000000)
![GitHub language count](https://img.shields.io/github/languages/count/RobertvanEijk-OW-251185/hollowEmber?color=000000)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/RobertvanEijk-OW-251185/hollowEmber?color=000000)

<!-- HEADER SECTION -->
<h5 align="center" style="padding:0;margin:0;">Robert van Eijk</h5>
<h5 align="center" style="padding:0;margin:0;">251185</h5>
<h6 align="center">DV200</h6>
</br>
<p align="center">

  <a href="https://github.com/RobertvanEijk-OW-251185/hollowEmber">
    <img src="./assets/faviconCamp.png" alt="Logo" width="140" height="140">
  </a>
  
  <h3 align="center">Hollow Ember</h3>

  <p align="center">
    Share your thoughts by the fire! <br>
      <!-- <a href="https://github.com/RobertvanEijk-OW-251185/holoowEmber"><strong>Explore the docs »</strong></a> -->
   <br />
   <br />
   <a href="https://drive.google.com/file/d/1S4tPeVz3jmfLwv1Mk5I70p_ewfFjaIcK/view?usp=sharing">View Demo</a>
</p>
<!-- TABLE OF CONTENTS -->
## Table of Contents

* [About the Project](#about-the-project)
  * [Project Description](#project-description)
  * [Built With](#built-with)
* [Getting Started](#getting-started)
  * [Prerequisites](#prerequisites)
  * [How to install](#how-to-install)
* [Features and Functionality](#features-and-functionality)
* [Concept Process](#concept-process)
   * [Ideation](#ideation)
   * [Wireframes](#wireframes)
* [Development Process](#development-process)
   * [Implementation Process](#implementation-process)
        * [Challenges](#challenges)
   * [Future Implementation](#peer-reviews)
* [Final Outcome](#final-outcome)
    * [Mockups](#mockups)
    * [Video Demonstration](#video-demonstration)
* [Roadmap](#roadmap)
* [Contributing](#contributing)
* [License](#license)
* [Contact](#contact)
* [Acknowledgements](#acknowledgements)

<!--PROJECT DESCRIPTION-->
## About the Project
<!-- header image of project -->
![Banner](./assets/Mockups/Banner.png)


### Project Description

Hollow Ember is a cozy, campfire-inspired web game designed to encourage spontaneous and meaningful conversations between players. After creating an account, players can choose a campfire and join a temporary discussion session with up to four participants. Once the fire is full, the session receives a randomly selected discussion topic, and any player can start the fire.

Each conversation lasts for three minutes. Players keep the discussion going by contributing messages, with a browser-based scoring system rewarding regular participation and applying penalties for inactivity. When the timer reaches zero, the fire burns out, the chat is locked, and the session is cleared after a short waiting period so the next group can begin with a new topic. Hollow Ember combines a cozy pixel-art camp setting with lightweight multiplayer interaction, timed sessions, and conversations that are not retained as a permanent chat history.

### Built With

* [XAMPP](path/to/technology/website)
* [PHP](path/to/technology/website)
* [MySQL](path/to/technology/website)
* [HTML](path/to/technology/website)
* [CSS](path/to/technology/website)
* [JavaScript](path/to/technology/website)

<!-- GETTING STARTED -->
<!-- Make sure to add appropriate information about what prerequisite technologies the user would need and also the steps to install your project on their own machines -->
## Getting Started

The following instructions will get a copy of the project up and running on your local machine for development and testing.

### Prerequisites

Ensure that you have the latest version of [XAMPP](https://www.apachefriends.org/download.html) installed on your machine.

### How to install

### Installation
Here are a couple of ways to clone this repo:

1. Clone Repository </br>
Run the following in the command line to clone the project:
   ```sh
   git clone https://github.com/RobertvanEijk-OW-251185/hollowEmber.git
   ```
    Note: Clone the repo into your XAMPP application folder within the htdocs folders

2. Run services in XAMPP Menu </br>
Run the following in the command line to install all the required dependencies:
   ```sh
   npm install
   ```

3. Open localhost server in your Browser </br>
Paste the following URL into your browser to open the application:
   ```sh
   [npm install](http://localhost/hollowEmber/)
   ```


<!-- FEATURES AND FUNCTIONALITY-->
<!-- You can add the links to all of your imagery at the bottom of the file as references -->
## Features and Functionality

### Log In and Sign Up

The logbook is implemented in `index.php` with two server-side POST flows. The sign-up form collects a username, email address, password, and password confirmation. PHP checks that all fields are filled and that both passwords match, hashes the password with `password_hash()`, and inserts the new account into the `users` table using a prepared statement. Creating an account displays a success message, but does not automatically log the player in.

The login form sends the username and password to the same PHP page. PHP looks up the account with a prepared statement and verifies the submitted password with `password_verify()`. On success, it stores the user ID and username in the PHP session and redirects the player to `pages/campGrounds.php`; invalid or incomplete credentials produce an error message. The protected camp pages check that session before allowing access.

![Login](assets/FeaturesImages/Login.png)

![Signup](assets/FeaturesImages/Signup.png)

### Camp Select

After login, `pages/campGrounds.php` displays four campfire choices. Each choice is a link containing a different `campfire` query parameter, which sends the player to the matching lobby in `pages/campfireLobby.php`. The campgrounds page requires an authenticated PHP session, and `js/campGrounds.js` attempts to start the looping forest-wind audio when the page loads. The commented-out logout link is not currently active.

![CampSelectBase](assets/FeaturesImages/CampSelect.png)

![CampSelectHover1](assets/FeaturesImages/CampSelectHover1.png)

![CampSelectHover3](assets/FeaturesImages/CampSelectHover3.png)

### Campfire Chat

When a player opens a lobby, PHP finds the latest `campfire_sessions` row for that campfire. If none exists, it creates a waiting session and assigns it a random row from `topics`. The player is then added to `session_participants` with their account name as the anonymous display name and an initial database score of zero. The lobby reads the current participants, topic, session status, and messages from MySQL.

The client polls `campfireLobby.php?action=get_players` every three seconds. Each response updates the player cards, topic, messages, scores, session status, and timer without reloading the page. The lobby uses a maximum of four players in JavaScript: until four players are present, the chat input and Start Fire button are disabled, and the topic is displayed as `Topic`. The server independently checks the participant count before accepting `start_fire`; only a full session in the `waiting` state can become `active`.

Messages are submitted asynchronously by `campfireLobby.js`. A non-empty message is inserted into `messages` with its participant and session IDs, and then the lobby is refreshed. The visible score is calculated in the browser from the messages currently returned by the database: the first message earns 50 points, later messages earn 50, 35, or 10 points based on the gap from the previous message, and inactivity applies a 12-point penalty for each completed 12-second interval. This score is presentation-only; the current JavaScript does not write the calculated score back to `contribution_score`.

Starting a fire sets `status` to `active` and records `start_time`. JavaScript displays a three-minute countdown based on that database timestamp. The body switches between the normal fire background and the burnout background at 90 seconds, and the fire-crackle audio is toggled when the fire starts. During polling, PHP changes an active session to `burned` after 180 seconds. The client then stops the timer and audio, resets the background, and locks the chat. After a burned session has been in that state for 10 seconds, PHP deletes its messages and participants, selects a new random topic, and resets the session to `waiting`.

The Leave Fire button sends `leave_session`, which deletes only the current user's participant row before returning to the campgrounds. The lobby also redirects back there when the polling response reports that post-fire cleanup is complete.

![CampfireRoom](assets/FeaturesImages/CampfireRoom.png)
![CampFireRoomFullStart](assets/FeaturesImages/CampFireRoomFullStart.png)
![CampFireRoomMessagesAndScore](assets/FeaturesImages/CampFireRoomMessagesAndScore.png)
![CampfireRoomBackgroundChange](assets/FeaturesImages/CampfireRoomBackgroundChange.png)
![CampfireRoomEnd](assets/FeaturesImages/CampfireRoomEnd.png)



<!-- CONCEPT PROCESS -->
<!-- Briefly explain your concept ideation process -->
## Concept Process

The `Conceptual Process` is the set of actions, activities, and research that were done when starting this project.

### Ideation

![Ideation](assets/Mockups/Ideation.png)

### Wireframes

![Wireframes](assets/Mockups/Wireframes.png)

<!-- DEVELOPMENT PROCESS -->
## Development Process

The `Development Process` is the technical implementation and functionality done in the frontend and backend of the application.

### Implementation Process
<!-- stipulate all of the functionality you included in the project -->

The application was implemented with a server-rendered PHP frontend, a MySQL database, and JavaScript for interactive behavior. The main implementation areas were:

* **Account authentication:** `index.php` handles sign-up and login requests. New passwords are securely hashed with PHP's `password_hash()` function, while login credentials are checked with `password_verify()`. Successful logins store the user's ID and username in a PHP session.
* **Database communication:** `php/db.php` creates the MySQL connection. Database queries use prepared statements and parameter binding to create users, find accounts, create or update campfire sessions, manage participants, select topics, and store messages.
* **Campfire sessions:** `pages/campfireLobby.php` creates or retrieves the latest session for a selected campfire and adds the logged-in user to `session_participants`. The server checks that a session has four players before changing its status from `waiting` to `active`.
* **Live lobby updates:** `js/campfireLobby.js` polls the lobby every three seconds and updates player cards, the topic, messages, scores, session status, and timer without reloading the whole page.
* **Chat and scoring:** Messages are submitted asynchronously and stored in the `messages` table. Scores are calculated in the browser from message timing: frequent messages receive more points, while periods of inactivity apply penalties. The displayed score is recalculated whenever the lobby is refreshed.
* **Timer and fire states:** The server records the start time and marks an active fire as `burned` after three minutes. JavaScript synchronizes the countdown with that timestamp, changes the fire background halfway through the session, and stops chat and audio when the fire ends.
* **Leaving and cleanup:** A player can leave through the Leave Fire button, which removes that user's participant row. Ten seconds after a fire burns out, the server removes the session's messages and participants, assigns a new topic, and returns the session to the waiting state.

The project uses a straightforward PHP page-and-script structure. PHP controls authentication, authorization, database operations, and session state, while JavaScript controls browser-side interaction, polling, animation state, audio, and live interface updates.

#### Challenges
<!-- stipulate the challenges you faced with the project and why you think you faced them or how you think you'll solve them (if not solved) -->
* There was a bug with player leaving, where I made the system load the users into an array, and when users left the campfire session, it would only remove the last added user from that array, instead of removing that specific user.
* There was an issue with getting messages and user updates within the session to update accordingly on all screens, but this was fixed by changing my thinking method on what the structure should be regarding this functionality. On the original fix, it reloaded the entire document, replayed every animation from the beginning, and reset the timer. The fix was to create a POLLING function that polls and updates the specified information pulled through from the DB at a set interval and have that only update the information itself and not the entire page.
* There was an issue with getting the account creation working throughout, as I wanted ot make use of a Form, but using a Form in the HTML would break the layout. This was solved by using JS to get the form data from the input fields, create a new form with the user's typed-in information, and submit that form to the database.
* On the campfire's background animations, I struggled with linking them to the timer and getting the timer to function properly, as I haven't used animations or made a timer before. Initially, I made use of a switch to change the background animation based on the timer's display time, but had to later on change that as the timer I had to link through multiple DB tables for messages, start times, and end times. I ended up setting the animation after doing math from the DB in JS with const variables in a syncTimer function where it swaps between background animation states depending wither the time is more than 90 seconds left or less than 90 seconds left.

### Future Implementation
<!-- stipulate functionality and improvements that can be implemented in the future. -->

* Logbook entering animation.
* Logbook opening animation.
* Page-turning animation in the logbook.
* Log-Out Sign-Post that users can click on in the Camp Grounds to log them out.

<!-- MOCKUPS -->
## Final Outcome

### Mockups

![Mockup1](assets/Mockups/Signup.png)
<br>
![Mockup2](assets/Mockups/Login.png)
<br>
![Mockup3](assets/Mockups/CampSelect.png)
<br>
![Mockup4](assets/Mockups/CampfireStart.png)
<br>
![Mockup6](assets/Mockups/CampfrieMiddel.png)
<br>
![Mockup7](assets/Mockups/CampfireEnd.png)

<!-- VIDEO DEMONSTRATION -->
### Video Demonstration

To see a run-through of the application, click below:

[View Demonstration](https://drive.google.com/file/d/1S4tPeVz3jmfLwv1Mk5I70p_ewfFjaIcK/view?usp=sharing)

<!-- ROADMAP -->
## Roadmap

See the [open issues](https://github.com/username/projectname/issues) for a list of proposed features (and known issues).

<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<!-- AUTHORS -->
## Authors

* **Robert Connor van Eijk** - [RobertvanEijk-OW-251185](https://github.com/RobertvanEijk-OW-251185)

<!-- LICENSE -->
## License

Distributed under the MIT License. See `LICENSE` for more information.\

<!-- LICENSE -->
## Contact

* **Robert Connor van Eijk** - [251185@virtualwindow.co.za](mailto:251185@virtualwindow.co.za)
* **Project Link** - https://github.com/RobertvanEijk-OW-251185/hollowEmber

<!-- ACKNOWLEDGEMENTS -->
## Acknowledgements
<!-- all resources that you used and Acknowledgements here -->
* [W3 Schools](https://www.w3schools.com/php/php_mysql_intro.asp)
* [W3 Schools](https://www.w3schools.com/php/php_ajax_php.asp)
* [W3 Schools](https://www.w3schools.com/php/php_ajax_database.asp)
* [W3 Schools](https://www.w3schools.com/php/php_ajax_poll.asp)
* [Stack Overflow](https://stackoverflow.com/questions/44983064/php-and-sql-select-from-database)
* [Stack Overflow](https://stackoverflow.com/questions/79928076/practical-tips-for-coding)
* [Stack Overflow](https://stackoverflow.com/questions/79889745/how-to-output-a-row-from-table-b-based-on-a-value-in-a-row-for-table-a-for-every)

<!-- MARKDOWN LINKS & IMAGES -->
[Banner]: assets/Mockups/Banner.png
[CampSelect]: assets/Mockups/CampSelect.png
[CampfireEnd]: assets/Mockups/CampfireEnd.png
[CampfireStart]: assets/Mockups/CampfireStart.png
[CampfrieMiddel]: assets/Mockups/CampfrieMiddel.png
[Ideation]: assets/Mockups/Ideation.png
[Login]: assets/Mockups/Login.png
[Signup]: assets/Mockups/Signup.png
[Wireframes]: assets/Mockups/Wireframes.png
[CampSelect]: assets/FeaturesImages/CampSelect.png
[CampSelectHover1]: assets/FeaturesImages/CampSelectHover1.png
[CampSelectHover3]: assets/FeaturesImages/CampSelectHover3.png
[CampfireRoom]: assets/FeaturesImages/CampfireRoom.png
[Login]: assets/FeaturesImages/Login.png
[Signup]: assets/FeaturesImages/Signup.png

