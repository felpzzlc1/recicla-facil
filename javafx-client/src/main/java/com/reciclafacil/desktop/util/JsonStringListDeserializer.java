package com.reciclafacil.desktop.util;

import com.fasterxml.jackson.core.JsonParser;
import com.fasterxml.jackson.core.type.TypeReference;
import com.fasterxml.jackson.databind.DeserializationContext;
import com.fasterxml.jackson.databind.JsonDeserializer;
import com.fasterxml.jackson.databind.ObjectMapper;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

public class JsonStringListDeserializer extends JsonDeserializer<List<String>> {
    
    private static final ObjectMapper mapper = new ObjectMapper();
    
    @Override
    public List<String> deserialize(JsonParser p, DeserializationContext ctxt) throws IOException {
        // Tentar deserializar como array primeiro
        if (p.getCurrentToken() == null) {
            p.nextToken();
        }
        
        // Se é um array JSON direto
        if (p.isExpectedStartArrayToken()) {
            return mapper.readValue(p, new TypeReference<List<String>>() {});
        }
        
        // Se é uma string que contém JSON
        String value = p.getValueAsString();
        if (value == null || value.trim().isEmpty()) {
            return new ArrayList<>();
        }
        
        // Se a string começa com [, é um JSON array em string
        if (value.trim().startsWith("[")) {
            return mapper.readValue(value, new TypeReference<List<String>>() {});
        }
        
        // Se é uma string simples, retornar como lista com um item
        List<String> result = new ArrayList<>();
        result.add(value);
        return result;
    }
}

