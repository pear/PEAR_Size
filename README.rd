# PEAR_SIZE
A commandline tool modeled on the Unix 'df' that lists how much filespace each
installed package consumes. A subset of packages can be specified as can
channels.

## Usage
    $ pear help size
    pear size [options] [PACKAGE ...]
    Display information on how much space an installed PEAR package requires.
    Options:
      -a, --all
            display information for all installed packages
      -A, --allchannels
            list packages from all channels, not just the default one
      -c CHANNEL, --channel=CHANNEL
            specify which channel
      -C, --csv
            output results in CSV format (sizes are measured in bytes).
      -h, --human-readable
            print sizes in human readable format (for example: 492 B 1KB 7MB)
      -H, --si
            likewise, but use powers of 1000 not 1024
      -t TYPES, --type=TYPES
            specify what type of files are required for the report
      -s, --summarise
            display channel summary view
      -S, --fsort
            sort by file size
      -v, --verbose
            display more detailed information
      -V, --version
            output version information and exit
      -X, --xml
            output results in XML format
      -0, --killzero
            do not output zero values in verbose mode


