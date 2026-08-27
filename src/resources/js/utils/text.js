/**
 * Utilitários de texto usados nas buscas do Semear.
 *
 * Centraliza a normalização das palavras para que as comparações não
 * diferenciem maiúsculas de minúsculas nem acentuação (ex.: "robô" e "robo").
 */

/**
 * Normaliza um texto para comparação, removendo acentos e diferenças de caixa.
 *
 * A decomposição em NFD separa cada letra do seu acento, permitindo descartar
 * os sinais diacríticos (faixa Unicode U+0300–U+036F) antes da comparação.
 *
 * @param {string} value - Texto a ser normalizado.
 * @returns {string} Texto em minúsculas e sem acentuação.
 */
export function normalizeText(value) {
    return String(value ?? '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase();
}
