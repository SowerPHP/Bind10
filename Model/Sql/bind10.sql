BEGIN;

ALTER TABLE zones ADD usuario INTEGER;
CREATE INDEX zones_usuario_idx ON zones (usuario);

COMMIT;
