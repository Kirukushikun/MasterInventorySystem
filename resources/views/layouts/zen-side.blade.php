<style>
  /* ── ZEN NAV ── */
:root {
  --bg: #0d1117;
  --surface: #161b22;
  --surface2: #1c2330;
  --border: #21262d;
  --text: #e6edf3;
  --muted: #7d8590;
  --accent: #2f81f7;
  --accent-glow: rgba(47,129,247,0.15);
}

.zen-nav {
  padding: 12px 0;
  list-style: none;
  margin: 0;
}

.zen-nav-item {
  margin: 0;
  padding: 0;
}

.zen-nav-link {
  display: flex !important;
  align-items: center;
  gap: 10px;
  padding: 8px 20px;
  color: var(--muted) !important;
  font-size: 13.5px;
  font-weight: 400;
  border-left: 2px solid transparent;
  text-decoration: none !important;
  transition: all 0.15s ease;
  cursor: pointer;
  /* override any framework defaults */
  background: transparent !important;
  border-radius: 0 !important;
  opacity: 1 !important;
}

.zen-nav-link:hover {
  color: var(--text) !important;
  background: rgba(255,255,255,0.03) !important;
}

.zen-nav-link.active {
  color: var(--accent) !important;
  background: var(--accent-glow) !important;
  border-left-color: var(--accent) !important;
  font-weight: 500;
}

.zen-nav-icon {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
  font-size: 14px;
  opacity: 0.7;
  /* prevent icon from growing */
  display: flex;
  align-items: center;
  justify-content: center;
}

.zen-nav-link.active .zen-nav-icon {
  opacity: 1;
}

.zen-nav-text {
  margin: 0 !important;
  line-height: 1.4;
  font-size: 13.5px;
  white-space: nowrap;
  text-transform: none !important;
  letter-spacing: normal !important;
}

.zen-nav-label {
    padding: 6px 20px;
    font-size: 10px;
    font-weight: 600;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 2px;
    list-style: none;
}
</style>