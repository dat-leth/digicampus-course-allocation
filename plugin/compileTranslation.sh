#!/bin/bash

# compileTranslation.sh
#
#  Copyright (c) 2016  Tobias Kolbe, Universitaet Augsburg
#
# This file is part of WebAuthPlugin.
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
# along with WebAuthPlugin.  If not, see <http://www.gnu.org/licenses/>.
#
# @author  Tobias Kolbe <tobias.kolbe@rz.uni-augsburg.de>
# @license GPL-3.0+ <http://spdx.org/licenses/GPL-3.0+>
# @since   1.0

set -e

#
#  2. Schritt: Kompilieren der Texte
#

# Pfad zu den Uebersetzungen
LOCALES=locale

# Name des Plugins
PLUGIN_NAME=bundleallocationplugin

# Liste der unterstuetzten Sprachen
LANGUAGES=(en)


for l in "${LANGUAGES[@]}"; do

    # Sichern der vorherigen mo-Datei
    if [ -f "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.mo" ]; then
        mv "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.mo" "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.mo.old"
    fi

    # Umwandlung der Datei in das Binaer-Format
    msgfmt "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.po" --output-file="$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.mo"

    # Entfernen der alten Datei
    rm -f "$LOCALES/$l/LC_MESSAGES/$PLUGIN_NAME.mo.old"
done
