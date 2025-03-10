<?php
/**
 * Copyright 2025 DomDev
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>
<?php
if (file_exists("saves.txt")) {
    $lines = file("saves.txt", FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        if (strpos($line, "[Admin]") === 0) {
            echo "<div class='message admin'>$line</div>";
        } elseif (strpos($line, "[You]") === 0) {
            echo "<div class='message sent'>$line</div>";
        } else {
            echo "<div class='message received'>$line</div>";
        }
    }
}
?>