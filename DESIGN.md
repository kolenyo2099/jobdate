---
name: Posted At
description: A calm evidence reader for public LinkedIn job timestamps.
colors:
  paper: "#f3f6f2"
  ink: "#102a35"
  muted: "#506871"
  rule: "#c2d2cb"
  teal: "#116970"
  teal-deep: "#084e56"
  wash: "#d9ece4"
  error: "#a02a38"
typography:
  display:
    fontFamily: "Atkinson Hyperlegible Next, Arial, sans-serif"
    fontSize: "clamp(2.5rem, 8vw, 4.7rem)"
    fontWeight: 700
    lineHeight: 0.93
    letterSpacing: "-0.04em"
  body:
    fontFamily: "Atkinson Hyperlegible Next, Arial, sans-serif"
    fontSize: "1.15rem"
    fontWeight: 400
    lineHeight: 1.48
rounded:
  field: "12px"
spacing:
  compact: "0.65rem"
  base: "1rem"
  section: "2.75rem"
components:
  button-primary:
    backgroundColor: "{colors.teal}"
    textColor: "#ffffff"
    rounded: "{rounded.field}"
    padding: "0.85rem 1.15rem"
  button-primary-hover:
    backgroundColor: "{colors.teal-deep}"
---

# Design System: Posted At

## Overview

**Creative North Star: "The Evidence Sheet"**

Posted At uses the restraint of a carefully marked research sheet: clear hierarchy, thin dividers, and one broad result field instead of dashboard-like cards. It should feel factual and composed, not surveillance-themed or bureaucratic.

## Colors

A pale green paper surface carries dark blue-green ink, with deep teal reserved for actions and status marks.

- **Paper** (`#f3f6f2`): page background.
- **Ink** (`#102a35`): primary copy and strong rules.
- **Muted** (`#506871`): explanatory copy and labels.
- **Teal** (`#116970`): primary action and note marker.
- **Error** (`#a02a38`): recovery states only.

## Typography

Atkinson Hyperlegible Next provides generous, legible forms for a small utility used for exact date checking. The display is heavy and tightly set; supporting prose stays open and unhurried.

- **Display:** 700 weight, `clamp(2.5rem, 8vw, 4.7rem)`, `0.93` line-height.
- **Body:** `1.15rem`, `1.48` line-height, with descriptions constrained to about 62 characters.
- **Labels:** uppercase, compact, and used only to identify a field or result.

## Layout

The single-column sheet is capped at 53rem. Content moves from a thin masthead to a large statement, then the one task form and its result. On narrow screens the input and button stack, while the reading order remains unchanged.

## Elevation & Depth

The system is flat. Boundaries come from rules and background tone, not floating panels. Inputs gain a restrained teal focus ring; buttons lift by one pixel only on hover.

## Shapes

Inputs and buttons use a 12px radius. Rules remain square and thin. The note uses a single vertical teal mark rather than a container.

## Components

### Buttons

- **Primary:** teal fill, white 700-weight label, 12px radius.
- **Hover / Focus:** deepens to teal-deep and rises 1px; keyboard focus is a high-contrast amber outline.

### Inputs / Fields

- **Style:** white fill, muted green-gray 1px border, 12px radius.
- **Focus:** teal border plus a four-pixel low-opacity teal ring.
- **Error / Disabled:** errors use the explicit red family; disabled actions desaturate and show a wait cursor.

### Result field

The result is a broad ruled region. Its timestamp is the largest data element; the device time-zone note and raw metadata sit beneath it in decreasing emphasis.

## Do's and Don'ts

- Do keep the extraction result, its time zone, and source metadata together.
- Do state the reposting limitation beside the result.
- Don't turn each status into a card or use generic analytics widgets.
- Don't add decorative gradients, glass effects, or pseudo-technical grids.
- Don't use teal for ordinary body copy; its scarcity makes the action and provenance mark legible.
