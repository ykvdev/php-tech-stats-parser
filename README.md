# Description

This project allows to receive PHP technologies hits statistics from vacancies for "PHP" keyword by site hh.ru. Also allows to generate graphical charts for visually view of received statistics. For example you can see results of generated charts by this link (ru): http://atoumus.github.io/php-tech-ratings.html

# Usage

If you want to run this project on your PC or server, you should follow next steps:
1. Clone this repository to your directory on PC or server.
1. Go to the root directory of this project and run composer installing for install necessary libraries for this project.
1. Run `php run.php get-stats-hh` for receive statistics from hh.ru vacancies.
1. Run `php run.php gen-charts` for generate chart pictures by received statistics.
1. Go to `results` directory and see the results.
