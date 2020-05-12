# Digicampus (Stud.IP) plugin for Bundle Allocation

Stud.IP plugin written in PHP 7. Extends Digicampus by:

- New Admission Rule
- Ability for teachers to see submitted priorities and preliminary allocation result
- Overwriting UI for submission of priorities
- Student overview of submitted priorities
- Cronjob for pending admission sets, communication with microservice in order to submit priorities, get status or fetch results.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

- Digicampus (Stud.IP 4.3 to 4.5 or higher)

### Installing

- Copy `./config-dist.inc.php` to `config.inc.php`. Edit file with endpoint URL of deployed microservice and bearer token.
- Create a ZIP archive containing the files of this folder in the root (except `./vue/` since it has been compiled to JavaScript bundles).
- Install plugin by usual methods of Digicampus (Stud.IP). Drag and Drop via Web UI or Installation via CLI.
- Activate new admission rule "Präferenzbasierte überschneidungsfreie Anmeldung" ("Preference-based non-overlapping admission")
- Activate necessary REST API endpoints.
- Generate Digicampus Private-Token for microservice.