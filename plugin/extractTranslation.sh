#!/bin/bash

# extractTranslation.sh
#
#  Copyright (c) 2016  Tobias Kolbe, Universitaet Augsburg
#
# This file is part of MultiAccountPlugin.
#
# Some open source application is free software: you can redistribute
# it and/or modify it under the terms of the GNU General Public
# License as published by the Free Software Foundation, either
# version 3 of the License, or (at your option) any later version.
#
# Some open source application is distributed in the hope that it will
# be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
# of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MultiAccountPlugin.  If not, see <http://www.gnu.org/licenses/>.
#
# @author  Tobias Kolbe <tobias.kolbe@rz.uni-augsburg.de>
# @license GPL-3.0+ <http://spdx.org/licenses/GPL-3.0+>
# @since   1.0

set -e

#
#  1. Schritt: Texte extrahieren und mit bestehenden Texten zusammenführen
#

# Relativer Pfad zu den Übersetzungen
LOCALES=locale

# Relativer Pfad zu den Code-Dateien
FILE_PATH=.

# Domain für gettext
PLUGIN_NAME=bundleallocationplugin

# Liste der unterstützten Sprachen
LANGUAGES=(en)

# Encoding der Texte
ENC=UTF-8


for l in "${LANGUAGES[@]}"; do
    
    # Sichern der bestehenden Texte und anlegen neuen leeren Datei
    if [ -f "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.po" ]; then
        mv "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.po" "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.po.old"
        touch "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.UTF-8.po"
    fi

    # Extrahieren aller Texte
    find "$FILE_PATH" \( -iname "*.php" \) | xargs xgettext --from-code="$ENC" -j -n --language=PHP \
        -o "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.UTF-8.po"

    # Umwandlung von UTF-8 zum angegebenen Encoding
    msgconv --to-code="$ENC" "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.UTF-8.po" \
        -o "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.po"

    # Zusammenführen der neuen Texte mit den bestehenden
    if [ -f "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.po.old" ]; then
        msgmerge "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.po.old" "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.po" \
            --output-file="$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.po"
    fi

    # Entfernen der alten Datei und der UTF-8 kodierten Datei
    rm -f "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.po.old" "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.UTF-8.po"
done
