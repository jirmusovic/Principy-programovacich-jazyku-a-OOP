##**Implementační dokumentace k 1. úloze do IPP 2022/2023<br>**
**Jméno a příjmení:** Jirmusová Veronika
**Login:** xjirmu00<br>

###**Struktura programu**

Moje řešení projektu sestává ze dvou .php souborů: instructions.php a parse php. Pomocný soubor instructions.php obsahuje enumeraci typů proměnných a pole s instrukcemi pro větší přehlednost. <br>

Hlavní soubor parse.php funguje na tom principu, že se hned po načtení vstupního souboru vytvoří dvourozměrné pole, do kterého se řadí data ze vstupního souboru. Každý řádek se rozdělí podle bílých znaků na jednotlivé instrukce, proměnné, apod., aby se k datům dalo jednoduše přistupovat, komentáře a přebytečné bíle znaky jsou smazány. Poté se zkontroluje přítomnost a korektnost hlavičky, která je pak smazána. Následně jsou vytvořeny proměnné s regulérními výrazy, díky kterým se kontroluje správnost argumentů u instrukcí. Každý argument ležící v poli za instrukcí je pomocí regulárních výrazu rozdělen na typy, kterým dle pravidel odpovídá.
Instrukce jsou pak rozděleny podle počtu a typu očekávaných argumentů do skupin, kde je veškerá korektnost ověřována a v případě nepřesnosti je vyvolána chyba a je okamžite vypsána hláška na chybový výstup.<br>

Pokud program chybu nenajde, pokračuje na generaci výstupního XML souboru. Pro generaci jsem zvolila knihovnu XMLWriter a pomocí cyklu procházela pomocné pole s instrukcemi a argumenty, kde jsem postupně nahrazovala všechny části pole za potřebné výstupní hodnoty.