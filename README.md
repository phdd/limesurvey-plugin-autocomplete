# LimeSurvey Autocomplete Plugin 

Enhances multiple choice questions by giving suggestions for the user's input.

## Installation

1. Copy the `FeedbackAutocomplete` into your `plugins` directory
2. Enable the plugin with the *Plugin Manager*  
3. Set question type to *List (Dropdown)*

Everytime a User types something into the generated input field, an asynchronous request fetches all options/answers for this question and suggests them.
When the given answer is a new one, it will be added to the question's options and suggested for further users.

## License

Copyright 2017 Peter Heisig

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
