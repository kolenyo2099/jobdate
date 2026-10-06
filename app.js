(() => {
  const endpoint = "api.php";
  const form = document.querySelector("#lookup-form");
  const input = document.querySelector("#job-url");
  const button = document.querySelector("#submit-button");
  const result = document.querySelector("#result");
  const content = result.querySelector(".result-content");

  function setLoading() {
    result.className = "result is-loading";
    content.textContent = "Reading the public job metadata…";
    button.disabled = true;
    button.textContent = "Looking…";
  }

  function setError(message) {
    result.className = "result is-error";
    content.replaceChildren();
    const title = document.createElement("p");
    title.className = "error-title";
    title.textContent = "Couldn’t extract a posting date";
    const detail = document.createElement("p");
    detail.className = "error-copy";
    detail.textContent = message;
    content.append(title, detail);
  }

  function setResult(payload) {
    const date = new Date(payload.datePosted);
    if (Number.isNaN(date.getTime())) {
      setError("The page returned a date in an unexpected format.");
      return;
    }

    const localDate = new Intl.DateTimeFormat(undefined, {
      dateStyle: "full",
      timeStyle: "long"
    }).format(date);

    result.className = "result is-ready";
    content.replaceChildren();
    const stamp = document.createElement("p");
    stamp.className = "timestamp";
    stamp.textContent = localDate;
    const zone = document.createElement("p");
    zone.className = "timezone";
    zone.textContent = `Shown in your device time zone (${Intl.DateTimeFormat().resolvedOptions().timeZone}).`;
    const raw = document.createElement("p");
    raw.className = "source-line";
    raw.append("LinkedIn metadata: ");
    const rawCode = document.createElement("code");
    rawCode.textContent = payload.datePosted;
    raw.append(rawCode);
    content.append(stamp, zone, raw);
  }

  function validate(url) {
    try {
      const parsed = new URL(url);
      return parsed.protocol === "https:" && (parsed.hostname === "linkedin.com" || parsed.hostname.endsWith(".linkedin.com")) && parsed.pathname.includes("/jobs/view/");
    } catch {
      return false;
    }
  }

  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    const url = input.value.trim();
    if (!validate(url)) {
      setError("Paste a full public LinkedIn job URL, such as https://www.linkedin.com/jobs/view/4451090264/.");
      input.focus();
      return;
    }

    setLoading();
    try {
      const response = await fetch(endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/json", "Accept": "application/json" },
        body: JSON.stringify({ url })
      });
      const payload = await response.json().catch(() => ({}));
      if (!response.ok || !payload.datePosted) {
        throw new Error(payload.error || "The extraction service returned no date.");
      }
      setResult(payload);
    } catch (error) {
      const hint = "The server could not retrieve that public listing. It may be unavailable, blocked, or missing datePosted metadata. Try again later or check the listing directly.";
      setError(error.message && !/Failed to fetch/i.test(error.message) ? `${error.message} ${hint}` : hint);
    } finally {
      button.disabled = false;
      button.textContent = "Find date";
    }
  });
})();
