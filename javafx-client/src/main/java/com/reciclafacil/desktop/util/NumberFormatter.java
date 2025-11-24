package com.reciclafacil.desktop.util;

import java.text.DecimalFormat;
import java.text.DecimalFormatSymbols;
import java.util.Locale;

public final class NumberFormatter {

    private static final DecimalFormatSymbols PT_BR_SYMBOLS = new DecimalFormatSymbols(new Locale("pt", "BR"));
    private static final DecimalFormat INTEGER_FORMAT = new DecimalFormat("#,##0", PT_BR_SYMBOLS);

    private NumberFormatter() {}

    public static String formatInt(Number value) {
        if (value == null) {
            return "0";
        }
        synchronized (INTEGER_FORMAT) {
            return INTEGER_FORMAT.format(value.longValue());
        }
    }
}

