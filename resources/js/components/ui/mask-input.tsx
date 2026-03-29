import * as React from "react"
import { cn } from "@/lib/utils"

type TokenKey = "9" | "a" | "*"

const DEFAULT_TOKENS: Record<TokenKey, RegExp> = {
  "9": /[0-9]/,
  "a": /[A-Za-z]/,
  "*": /[A-Za-z0-9]/,
}

type MaskInputProps = Omit<React.ComponentProps<"input">, "onChange" | "value" | "defaultValue"> & {
  mask: string
  /** Masked value if you want to control the input */
  value?: string
  /** Masked default value (uncontrolled mode) */
  defaultValue?: string
  /** Called whenever value changes (gives both masked & unmasked) */
  onValueChange?: (v: { masked: string; unmasked: string }) => void
  /** Override/extend token rules if needed */
  tokens?: Partial<Record<TokenKey, RegExp>>

  onChange?: any
}

export const MaskInput = React.forwardRef<HTMLInputElement, MaskInputProps>(
  (
    {
      className,
      mask,
      value,
      defaultValue,
      onValueChange,
      onChange, // still forwarded (receives masked value)
      onKeyDown,
      tokens,
      ...props
    },
    ref
  ) => {
    const TOKENS = React.useMemo(() => ({ ...DEFAULT_TOKENS, ...(tokens || {}) }), [tokens])

    const inputRef = React.useRef<HTMLInputElement | null>(null)
    React.useImperativeHandle(ref, () => inputRef.current as HTMLInputElement)

    // Helpers to identify mask slots vs literals
    const isToken = React.useCallback((c: string): c is TokenKey => (["9", "a", "*"] as string[]).includes(c), [])
    const tokenList = React.useMemo(() => mask.split("").filter(isToken), [mask, isToken])

    // Build masked string from an UNMASKED value
    const maskify = React.useCallback(
      (raw: string) => {
        let ri = 0
        let out = ""
        for (const m of mask) {
          if (isToken(m)) {
            // advance raw until something satisfies the token (or end)
            while (ri < raw.length && !TOKENS[m].test(raw[ri])) ri++
            if (ri < raw.length && TOKENS[m].test(raw[ri])) {
              out += raw[ri]
              ri++
            } else {
              // empty slot -> stop placing more literals after this point
              break
            }
          } else {
            out += m
          }
        }
        return out
      },
      [mask, TOKENS, isToken]
    )

    // Extract UNMASKED from any masked-looking string
    const unmaskify = React.useCallback(
      (maskedStr: string) => {
        const rawChars: string[] = []
        let mi = 0
        for (const m of mask) {
          if (mi >= maskedStr.length) break
          const ch = maskedStr[mi]
          if (isToken(m)) {
            if (TOKENS[m].test(ch)) rawChars.push(ch)
            else {
              // if the current character doesn't match, skip it (e.g. on paste)
              if (TOKENS[m].test(ch)) rawChars.push(ch)
            }
            mi++
          } else {
            // literal: keep it in masked, but don't include in raw
            if (ch === m) {
              mi++
            } else {
              // If user pasted without literals, don't increment mi (we'll keep scanning)
            }
          }
        }
        return rawChars.join("")
      },
      [mask, TOKENS, isToken]
    )

    // Derive initial state (uncontrolled)
    const [maskedState, setMaskedState] = React.useState<string>(() => {
      if (typeof value === "string") return value
      if (typeof defaultValue === "string") return defaultValue
      return ""
    })

    const masked = typeof value === "string" ? value : maskedState
    const unmasked = React.useMemo(() => {
      const onlySlots = mask.split("").filter(isToken).length
      const extracted = (masked || "").split("").filter(Boolean)
      // more robust: use unmaskify then truncate to slot count
      return unmaskify(masked).slice(0, onlySlots)
    }, [masked, mask, isToken, unmaskify])

    // Place caret after the last filled slot (or right before the next literal)
    const placeCaret = React.useCallback((el: HTMLInputElement | null, mstr: string) => {
      if (!el) return
      // Find the index after the last token character
      const pos = mstr.length
      // Avoid placing caret inside trailing literals beyond last filled slot
      // (e.g., "+92 (" shouldn't leave caret after the space if there's no digit)
      el.setSelectionRange(pos, pos)
    }, [])

    // Main input change handler
    const handleChange = React.useCallback(
      (e: React.ChangeEvent<HTMLInputElement>) => {
        const typed = e.currentTarget.value
        // Convert typed string -> raw -> masked (clean & align)
        const raw = unmaskify(typed)
        const nextMasked = maskify(raw)

        if (value === undefined) setMaskedState(nextMasked)

        // Fire both callbacks
        onValueChange?.({ masked: nextMasked, unmasked: raw })
        onChange?.({
          ...e,
          target: { ...e.target, value: nextMasked },
          currentTarget: { ...e.currentTarget, value: nextMasked },
        } as React.ChangeEvent<HTMLInputElement>)

        // Place caret nicely on next frame
        requestAnimationFrame(() => placeCaret(inputRef.current, nextMasked))
      },
      [maskify, onChange, onValueChange, placeCaret, unmaskify, value]
    )

    // Handle backspace over literals (jump left to previous slot)
    const handleKeyDown = React.useCallback(
      (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key !== "Backspace") {
          onKeyDown?.(e)
          return
        }
        const el = e.currentTarget
        const { selectionStart, selectionEnd, value: current } = el
        if (selectionStart !== selectionEnd) {
          onKeyDown?.(e)
          return
        }
        // If at a literal, move left until a slot char or beginning
        let pos = (selectionStart ?? current.length) - 1
        while (pos >= 0 && !isToken(mask[pos])) {
          pos--
        }
        if (pos >= 0) {
          // Remove that slot from RAW and rebuild
          const raw = unmaskify(current)
          const slotIndexOrder: number[] = []
          for (let i = 0; i < mask.length; i++) if (isToken(mask[i])) slotIndexOrder.push(i)
          const deleteIndex = slotIndexOrder.indexOf(pos)
          if (deleteIndex >= 0) {
            const newRaw = raw.slice(0, deleteIndex) + raw.slice(deleteIndex + 1)
            const nextMasked = maskify(newRaw)
            if (value === undefined) setMaskedState(nextMasked)
            onValueChange?.({ masked: nextMasked, unmasked: newRaw })
            onKeyDown?.(e)
            e.preventDefault()
            requestAnimationFrame(() => {
              const nextPos = Math.min(pos, nextMasked.length)
              el.setSelectionRange(nextPos, nextPos)
            })
            return
          }
        }
        onKeyDown?.(e)
      },
      [mask, isToken, maskify, onKeyDown, onValueChange, unmaskify, value]
    )

    // Ensure caret placement on external value changes (controlled)
    React.useEffect(() => {
      placeCaret(inputRef.current, masked || "")
    }, [masked, placeCaret])

    // Ensure any paste is normalized (let default happen, our onChange will clean it)
    const handlePaste = React.useCallback(() => {
      // No-op: onChange path will normalize paste contents automatically
      // (This function exists just to document behavior)
    }, [])

    return (
      <input
        ref={inputRef}
        type="text"
        data-slot="input"
        value={masked}
        onChange={handleChange}
        onKeyDown={handleKeyDown}
        onPaste={handlePaste}
        className={cn(
          "border-input file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground flex h-9 w-full min-w-0 rounded-md border bg-white dark:bg-neutral-950 px-3 py-1 text-neutral-700 dark:text-white shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm",
          "focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]",
          "aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive",
          className
        )}
        {...props}
      />
    )
  }
)

MaskInput.displayName = "MaskInput"
