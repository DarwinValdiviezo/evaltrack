# ci-cd-pipeline.yml

Este archivo es el pipeline de CI/CD en GitHub Actions. Aquí se definen todos los pasos automáticos que se hacen cuando subimos código: compilar, probar, analizar seguridad, desplegar y monitorear. Así todo el equipo sigue el mismo proceso y se evitan errores manuales.

## Fragmento importante:
```yaml
jobs:
  build-and-test:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout code
        uses: actions/checkout@v4
      - name: Setup Node.js
        uses: actions/setup-node@v4
      - name: Install dependencies
        run: npm ci
```

## ¿Por qué es importante?
- Automatiza todo el flujo de despliegue.
- Asegura calidad y seguridad antes de pasar a producción.
- Permite rollback y monitoreo automático.

**Recomendación:**
Revisa los logs de cada etapa y usa ramas para probar antes de pasar a main. 