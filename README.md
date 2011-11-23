Mixxxx.es
==========

About
-----
Mixxxx.es streams music from youtube via reddit and user submissions. It was initially hacked together messily in about 2 hours. The main intended audience for this is a few friends.

By default it streams reddit.com/r/futuregarage and uses a simple database backend to authorise user logins in order to store user submissions. Admins can add boards and add/delete tunes from each board.

Changelog
------

### 23/11/2011
  - Fixed the r/futuregarge stream.
  - Reddit API requests now cached on server and refreshed after 5 minutes.
  - No longer using DOM parser.
  - Accessing youtube URL directly rather than the embed HTML from reddit API.
  - Removed require instances for the DOM parser.
  - db.php should now only be required once (was multiple).
  - Cleaned up reddit.php code, reduced also.
  - Added couple things to to-dos.
  - Reddit youtube urls now stripped out if blank after parsing.

### 22/11/2011
  - Added numerous things to the to-do list below.

To-dos
------

- Limit & cache requests. (reddit asks for no more than 0.5req/sec).
- Pagination for boards.
- Find an alternative/better way to extract the youtube video URL rather than using the DOM parser.
- Add URL support for each board (e.g. mixxxx.es/#future-g).
- Add ability for guests to submit/add songs into the backend (but not modify/delete songs or boards).
- Potentially add support for comments.
- Significant PHP cleanup.
-- Add some security to SQL statements.
-- General organisation and code improvement.
- Add HTML5 BP.
- Remove crappy comments, messy debugging.
- Support youtu.be links on reddit.

Licensing
---------

Re-use of my code is fine under a Creative Commons 3.0 [Non-commercial, Attribution, Share-Alike](http://creativecommons.org/licenses/by-nc-sa/3.0/) license. In short, this means that you can use my code, modify it, do anything you want. Just don't sell it and make sure to give me a shout-out.



