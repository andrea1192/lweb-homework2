// display.c
// 
// Legge file testuali interpretando la query string di una richiesta HTTP:
//  1. se contiene STYLE_PTR, la richiesta riguarda uno stylesheet (text/css)
//  2. altrimenti, riguarda una pagina web (text/html)
//
// Nel caso (1) viene letto il file puntato da STYLE_PTR.
// Nel caso (2) viene letto TEMPLATE_FILE, sostituendo CONTENT_LABEL con il 
// contenuto del file puntato da PAGE_PTR, se specificato, o di DEFAULT_CONTENT.
//
// Tutti i file che devono essere letti vengono cercati nella document root.
// Le variabili d'ambiente che contengono document root e query string possono
// essere sovrascritte inserendole nel file ENV_OVERRIDES, che è letto ad
// ogni avvio del programma.
//
// Limitazioni:
// 1. Solo i media type text/css e text/html sono supportati.
// 2. Le righe contenenti CONTENT_LABEL verranno sostituite integralmente, anche
// se contengono altro testo oltre a CONTENT_LABEL. Non si è fatto uso di
// espressioni regolari per mantenere la compatibilità con C99, in modo da
// poter compilare anche per Windows.
//
// Testato con GCC 12.2.0 (Linux) ed 11.4.0 (Windows/MinGW-w64)

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#define SPLIT_INIT_SIZE 8
#define SPLIT_STEP_SIZE 16
#define RFILE_LINEBUF_SIZE 160

#define ENV_OVERRIDES "overrides.env"
#define CONTENT_LABEL "{{content}}"
#define DEFAULT_CONTENT "main"
#define TEMPLATE_FILE "template"
#define PAGE_PTR "page"
#define PAGE_EXT "html"
#define STYLE_PTR "style"
#define STYLE_EXT "css"

#define strdup(s) __strdup(s)

typedef enum {SUCCESS, ROOT_MISSING, MALLOC_FAILED, FOPEN_FAILED, FEOF_FAILED} 
		RFILE_ERROR;

// Restituisce un duplicato della stringa src - analogo a strdup di POSIX/C23
// La memoria allocata per il dupl. dovrà essere rilasciata dall'utilizzatore
char *__strdup(const char *src) {
	size_t srclen = strlen(src) + 1;
	char *dest = malloc(sizeof(*dest) * srclen);
	if (!dest) return NULL;

	return memcpy(dest, src, srclen);
}

// Restituisce un array di stringhe contenute in str e separate da sep
// La memoria allocata per l'array dovrà essere rilasciata dall'utilizzatore
char **split(char *str, const char *sep) {
	char **tokens = NULL;
	char *token = NULL;
	unsigned int count = 0;
	size_t size = SPLIT_INIT_SIZE;

	tokens = malloc(sizeof(*tokens) * size);
	if (!tokens) return NULL;

	for (token = strtok(str, sep); token; token = strtok(NULL, sep)) {
		tokens[count++] = token;
		
		if (count == size) {
			tokens = realloc(
					tokens, sizeof(*tokens) * (size += SPLIT_STEP_SIZE));
			if (!tokens) return NULL;
		}
	}

	tokens[count] = NULL;

	return realloc(tokens, sizeof(*tokens) * (count + 1));
}

// Restituisce un duplicato dell'argomento key della query string, oppure NULL
// La memoria allocata per il dupl. dovrà essere rilasciata dall'utilizzatore
char *get_value(const char *key) {
	char *qstring = NULL;
	char **args = NULL;
	char *argstring = NULL;
	char **argpair = NULL;
	char *argk = NULL;
	char *argv = NULL;
	char *rval = NULL;

	qstring = getenv("QUERY_STRING");
	if (!qstring) return NULL;

	qstring = strdup(qstring);
	args = split(qstring, "&");
	if (!args) return NULL;

	for (int i = 0; args[i]; i++) {
		argstring = strdup(args[i]);
		argpair = split(argstring, "=");

		if (!argpair || !argpair[0] || !argpair[1]) continue;
		argk = argpair[0];
		argv = argpair[1];

		if (!strcmp(argk,key)) rval = strdup(argv);

		free(argstring);
		free(argpair);
	}

	free(qstring);
	free(args);
	return rval;
}

// Carica variabili d'ambiente dal file fname, eventualmente sovrascrivendo 
// quelle già definite. La memoria allocata per le variabili non va rilasciata.
RFILE_ERROR read_env_overrides(const char *fname) {
	FILE *fp;
	char buf[RFILE_LINEBUF_SIZE];

	fp = fopen(fname, "r");
	if (!fp) return FOPEN_FAILED;

	while (fgets(buf, sizeof(buf), fp)) {

		if (buf[0] == '#') continue;

		if (strstr(buf, "="))
			putenv(strdup(strtok(buf, "\n")));
	}

	if (!feof(fp)) return FEOF_FAILED;

	fclose(fp);
	return SUCCESS;
}

// Legge il file name una linea alla volta, cercando CONTENT_LABEL all'interno.
// Quando lo trova, effettua una chiamata ricorsiva per leggere il file content,
// se definito. I file sono cercati nella document root.
RFILE_ERROR read_file(const char *name, const char *extension, const char *content) {
	char *root = NULL;
	char *path = NULL;
	size_t plen = 0;
	FILE *fp;
	char buf[RFILE_LINEBUF_SIZE];

	root = getenv("DOCUMENT_ROOT");
	if (!root) return ROOT_MISSING;

	root = strdup(root);
	plen = strlen(root) + strlen(name) + strlen(extension) + 3; // '/'+'.'+'\0'
	path = malloc(sizeof(*path) * plen);
	if (!path) return MALLOC_FAILED;

	snprintf(path, plen, "%s/%s.%s", root, name, extension);
	fp = fopen(path, "r");
	if (!fp) return FOPEN_FAILED;

	while (fgets(buf, sizeof(buf), fp)) {

		if (strstr(buf, CONTENT_LABEL)) {

			if (content && read_file(content, extension, NULL)) 
				printf("Errore nella lettura di \"%s\"\n", content);

			continue;
		}

		printf("%s", buf);
	}

	printf("\n");

	if (!feof(fp)) return FEOF_FAILED;

	fclose(fp);
	free(path);
	free(root);
	return SUCCESS;
}

int main() {
	char *content = NULL;
	char *extension = NULL;

	read_env_overrides(ENV_OVERRIDES);

	if (get_value(STYLE_PTR)) {
		printf("Content-type: text/css; charset=iso-8859-1\n\n");

		content = get_value(STYLE_PTR);
		extension = STYLE_EXT;

		read_file(content, extension, NULL);
	} else {
		printf("Content-type: text/html; charset=iso-8859-1\n\n");

		content = get_value(PAGE_PTR);
		extension = PAGE_EXT;

		if (!content)
			content = DEFAULT_CONTENT;

		read_file(TEMPLATE_FILE, extension, content);
	}

	return 0;
}
