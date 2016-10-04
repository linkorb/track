Track: CLI based time-tracking
==============================

track helps you to keep track of your time from the comfort of your own cli.

logs are saved as .json files in `~/.track`. It's recommeded to version control these in git.

A log consists of:

* id: unique number
* message: description, including hash-tags, mentions and other classifiers
* started at: date+time the activity started
* ended at: date+time the activity ended

## Usage:

### Log an activity:

    track log # the app will ask for input of message, start and/or end times

Track will make an effort to pre-fill start+end times with sensible defaults
based on previous logs.
You can specify any natural start/end time such as `11:20`, `-5 minutes`, `-1 hour`, etc

### List activities

    track list
    
### View activity details

    track view 12
    
### Edit activity details

    track edit 12
    
### Delete activity

    track delete 12
    
### Reporting activity

    track report # show totals per category
    track report -b # show --breakdown of logs per category
    
## License

MIT. Please refer to the [license file](LICENSE) for details.

## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).

Btw, we're hiring!
