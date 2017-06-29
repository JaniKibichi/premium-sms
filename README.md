# Setting Up a Premium SMS Sending Service for MT Short Codes
#### General outline for sending

- moveSubs.php, fetchsubscribers.php and premiumsms.php are run through cron jobs
- More information from [Africa's Talking API](docs.africastalking.com/sms). 
- The following are needed:

| From AT                                      |
| --------------------------------------------:| 
| Shortcode                                    | 
| Keyword on Short code                        |   
| Callback for receiving subscriptions         |   


## Prerequisites
- Update the premiumConfig.php file and the dbCon.php (database connection) in your root directory and fill in your Africa's Talking API credentials...

#### Database tables
- Subscribers table:

`CREATE TABLE subscribers ( id int(6) NOT NULL AUTO_INCREMENT, phone_number varchar(25),lastrecvd_id varchar(25),PRIMARY KEY (id));`

- Sending table:

`CREATE TABLE sending (id varchar(50),phone_number varchar(25),status varchar(25),lastrecvd_id varchar(100));`

- Countsent table:

`CREATE TABLE countsent (numbers_sent_to int(5),insert_time varchar(40));`

- Quotes table:

`CREATE TABLE quotes (id varchar(50), message varchar(250));`