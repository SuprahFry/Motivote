# MOTIVOTE - ULTIMATE VOTE REWARD SCRIPT #

![Motivote](http://i.imgur.com/yvGrXLM.png)
![Motivote](http://i.imgur.com/pQSpAJP.png)

### How do I get set up? ###

* Download latest version of Motivote (you probably already have, since you're reading this!)
* Upload them to your web host
* Enter the credentials for your MySQL database in config.php (there is a sample file called 'config.sample.php')
* Import the SQL provided in the release
* Make sure if this is a fresh install to reset the password! You can do so by running the query below (scroll down).
* In your browser, go to the folder you just uploaded the script to. We want to be in the admin control panel. For example, http://example.com/vote/admin/
* Change the settings to your liking and you're all done!

### How do I reset my admin password? ###

Run this query, make sure to edit '!!yournewpass!!' to the password you want. You will be able to edit this from inside the control panel once you have access.

	UPDATE `mv_preferences` SET `value` = MD5('!!yournewpass!!') WHERE `name` = 'admin_pass';

http://motivoters.com/
