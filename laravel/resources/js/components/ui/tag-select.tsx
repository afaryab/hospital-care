import * as React from "react"
import { XIcon } from "lucide-react"
import { cn } from "@/lib/utils"

interface TagSelectOption {
  value: string
  label: string
  disabled?: boolean
}

interface TagSelectProps {
  options: TagSelectOption[]
  value?: string[]
  onValueChange?: (value: string[]) => void
  placeholder?: string
  className?: string
  disabled?: boolean
  allowCustom?: boolean
  maxItems?: number
  onSearch?: (query: string) => void
}

export function TagSelect({
  options,
  value = [],
  onValueChange,
  placeholder = "Type to search and select...",
  className,
  disabled = false,
  allowCustom = false,
  maxItems,
  onSearch,
}: TagSelectProps) {
  const [inputValue, setInputValue] = React.useState("")
  const [isOpen, setIsOpen] = React.useState(false)
  const [filteredOptions, setFilteredOptions] = React.useState(options)
  const inputRef = React.useRef<HTMLInputElement>(null)
  const containerRef = React.useRef<HTMLDivElement>(null)

  // Filter options based on search input
  React.useEffect(() => {
    const filtered = options.filter((option) => {
      const isAlreadySelected = value.includes(option.value)
      const matchesSearch = option.label.toLowerCase().includes(inputValue.toLowerCase())
      return !isAlreadySelected && matchesSearch
    })
    setFilteredOptions(filtered)
    
    // Call external search handler if provided
    if (onSearch) {
      onSearch(inputValue)
    }
  }, [inputValue, options, value, onSearch])

  // Handle clicking outside to close dropdown
  React.useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (containerRef.current && !containerRef.current.contains(event.target as Node)) {
        setIsOpen(false)
      }
    }

    document.addEventListener("mousedown", handleClickOutside)
    return () => {
      document.removeEventListener("mousedown", handleClickOutside)
    }
  }, [])

  const handleSelect = (optionValue: string, optionLabel?: string) => {
    if (maxItems && value.length >= maxItems) return
    
    const newValue = [...value, optionValue]
    onValueChange?.(newValue)
    setInputValue("")
    setIsOpen(false)
    inputRef.current?.focus()
  }

  const handleRemove = (optionValue: string) => {
    const newValue = value.filter((v) => v !== optionValue)
    onValueChange?.(newValue)
    inputRef.current?.focus()
  }

  const handleKeyDown = (e: React.KeyboardEvent) => {
    if (e.key === "Enter" && inputValue.trim()) {
      e.preventDefault()
      
      // Check if there's an exact match in filtered options
      const exactMatch = filteredOptions.find(
        option => option.label.toLowerCase() === inputValue.toLowerCase()
      )
      
      if (exactMatch) {
        handleSelect(exactMatch.value)
      } else if (allowCustom && inputValue.trim()) {
        // Add custom value
        const customValue = inputValue.trim()
        if (!value.includes(customValue)) {
          handleSelect(customValue, customValue)
        }
      } else if (filteredOptions.length > 0) {
        // Select first filtered option
        handleSelect(filteredOptions[0].value)
      }
    }
    
    if (e.key === "Backspace" && inputValue === "" && value.length > 0) {
      // Remove last tag when backspacing with empty input
      handleRemove(value[value.length - 1])
    }
    
    if (e.key === "Escape") {
      setIsOpen(false)
      setInputValue("")
    }
    
    if (e.key === "ArrowDown" || e.key === "ArrowUp") {
      e.preventDefault()
      setIsOpen(true)
    }
  }

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const newValue = e.target.value
    setInputValue(newValue)
    setIsOpen(newValue.length > 0 || filteredOptions.length > 0)
  }

  const handleInputFocus = () => {
    setIsOpen(true)
  }

  const selectedOptions = options.filter(option => value.includes(option.value))
  const showDropdown = isOpen && (filteredOptions.length > 0 || (allowCustom && inputValue.trim()))

  return (
    <div ref={containerRef} className={cn("relative", className)}>
      {/* Input container with tags */}
      <div
        className={cn(
          "border-input focus-within:border-ring focus-within:ring-ring/50 focus-within:ring-[3px] min-h-9 w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] has-[:disabled]:cursor-not-allowed has-[:disabled]:opacity-50",
          "flex flex-wrap items-center gap-1"
        )}
      >
        {/* Selected tags */}
        {value.map((selectedValue) => {
          const option = options.find(opt => opt.value === selectedValue)
          const displayLabel = option?.label || selectedValue
          
          return (
            <span
              key={selectedValue}
              className="bg-primary/10 text-primary inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium"
            >
              {displayLabel}
              <button
                type="button"
                onClick={(e) => {
                  e.stopPropagation()
                  handleRemove(selectedValue)
                }}
                className="hover:bg-primary/30 rounded-full p-0.5"
                disabled={disabled}
              >
                <XIcon className="h-3 w-3" />
              </button>
            </span>
          )
        })}

        {/* Input field */}
        <input
          ref={inputRef}
          type="text"
          value={inputValue}
          onChange={handleInputChange}
          onKeyDown={handleKeyDown}
          onFocus={handleInputFocus}
          placeholder={value.length === 0 ? placeholder : ""}
          className="flex-1 min-w-[120px] bg-transparent outline-none placeholder:text-muted-foreground"
          disabled={disabled || (maxItems !== undefined && value.length >= maxItems)}
        />
      </div>

      {/* Dropdown */}
      {showDropdown && (
        <div className="absolute top-full left-0 right-0 z-50 mt-1 max-h-60 overflow-auto rounded-md border bg-popover p-1 shadow-md">
          {filteredOptions.length === 0 && allowCustom && inputValue.trim() ? (
            // Show "Add custom" option
            <div
              className="hover:bg-accent hover:text-accent-foreground relative flex cursor-pointer items-center rounded-sm px-2 py-1.5 text-sm outline-none"
              onClick={() => handleSelect(inputValue.trim())}
            >
              <span className="text-muted-foreground">Add "</span>
              <span className="font-medium">{inputValue.trim()}</span>
              <span className="text-muted-foreground">"</span>
            </div>
          ) : (
            // Show filtered options
            filteredOptions.map((option) => (
              <div
                key={option.value}
                className={cn(
                  "hover:bg-accent hover:text-accent-foreground relative flex cursor-pointer items-center rounded-sm px-2 py-1.5 text-sm outline-none",
                  option.disabled && "pointer-events-none opacity-50"
                )}
                onClick={() => !option.disabled && handleSelect(option.value)}
              >
                {option.label}
              </div>
            ))
          )}
          
          {filteredOptions.length === 0 && (!allowCustom || !inputValue.trim()) && (
            <div className="text-muted-foreground relative flex items-center justify-center rounded-sm px-2 py-1.5 text-sm">
              No options found
            </div>
          )}
        </div>
      )}
    </div>
  )
}

// Alternative with more advanced features
export function AdvancedTagSelect({
  options,
  value = [],
  onValueChange,
  placeholder = "Type to search and select...",
  className,
  disabled = false,
  allowCustom = false,
  maxItems,
  onSearch,
}: TagSelectProps) {
  const [inputValue, setInputValue] = React.useState("")
  const [isOpen, setIsOpen] = React.useState(false)
  const [highlightedIndex, setHighlightedIndex] = React.useState(0)
  const [filteredOptions, setFilteredOptions] = React.useState(options)
  const inputRef = React.useRef<HTMLInputElement>(null)
  const containerRef = React.useRef<HTMLDivElement>(null)
  const optionsRef = React.useRef<(HTMLDivElement | null)[]>([])

  React.useEffect(() => {
    const filtered = options.filter((option) => {
      const isAlreadySelected = value.includes(option.value)
      const matchesSearch = option.label.toLowerCase().includes(inputValue.toLowerCase())
      return !isAlreadySelected && matchesSearch
    })
    setFilteredOptions(filtered)
    setHighlightedIndex(0)
    
    if (onSearch) {
      onSearch(inputValue)
    }
  }, [inputValue, options, value, onSearch])

  React.useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (containerRef.current && !containerRef.current.contains(event.target as Node)) {
        setIsOpen(false)
      }
    }

    document.addEventListener("mousedown", handleClickOutside)
    return () => {
      document.removeEventListener("mousedown", handleClickOutside)
    }
  }, [])

  const handleSelect = (optionValue: string) => {
    if (maxItems && value.length >= maxItems) return
    
    const newValue = [...value, optionValue]
    onValueChange?.(newValue)
    setInputValue("")
    setIsOpen(false)
    inputRef.current?.focus()
  }

  const handleRemove = (optionValue: string) => {
    const newValue = value.filter((v) => v !== optionValue)
    onValueChange?.(newValue)
    inputRef.current?.focus()
  }

  const handleKeyDown = (e: React.KeyboardEvent) => {
    if (e.key === "Enter" && isOpen) {
      e.preventDefault()
      
      if (filteredOptions[highlightedIndex]) {
        handleSelect(filteredOptions[highlightedIndex].value)
      } else if (allowCustom && inputValue.trim()) {
        handleSelect(inputValue.trim())
      }
    }
    
    if (e.key === "ArrowDown") {
      e.preventDefault()
      setIsOpen(true)
      setHighlightedIndex(prev => 
        prev < filteredOptions.length - 1 ? prev + 1 : prev
      )
    }
    
    if (e.key === "ArrowUp") {
      e.preventDefault()
      setHighlightedIndex(prev => prev > 0 ? prev - 1 : 0)
    }
    
    if (e.key === "Backspace" && inputValue === "" && value.length > 0) {
      handleRemove(value[value.length - 1])
    }
    
    if (e.key === "Escape") {
      setIsOpen(false)
      setInputValue("")
    }
  }

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const newValue = e.target.value
    setInputValue(newValue)
    setIsOpen(true)
  }

  const showDropdown = isOpen && (filteredOptions.length > 0 || (allowCustom && inputValue.trim()))

  return (
    <div ref={containerRef} className={cn("relative", className)}>
      <div
        className={cn(
          "border-input focus-within:border-ring focus-within:ring-ring/50 focus-within:ring-[3px] min-h-9 w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] has-[:disabled]:cursor-not-allowed has-[:disabled]:opacity-50",
          "flex flex-wrap items-center gap-1"
        )}
      >
        {value.map((selectedValue) => {
          const option = options.find(opt => opt.value === selectedValue)
          const displayLabel = option?.label || selectedValue
          
          return (
            <span
              key={selectedValue}
              className="bg-primary/10 text-primary inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium"
            >
              {displayLabel}
              <button
                type="button"
                onClick={(e) => {
                  e.stopPropagation()
                  handleRemove(selectedValue)
                }}
                className="hover:bg-primary/30 rounded-full p-0.5"
                disabled={disabled}
              >
                <XIcon className="h-3 w-3" />
              </button>
            </span>
          )
        })}

        <input
          ref={inputRef}
          type="text"
          value={inputValue}
          onChange={handleInputChange}
          onKeyDown={handleKeyDown}
          onFocus={() => setIsOpen(true)}
          placeholder={value.length === 0 ? placeholder : ""}
          className="flex-1 min-w-[120px] bg-transparent outline-none placeholder:text-muted-foreground"
          disabled={disabled || (maxItems !== undefined && value.length >= maxItems)}
        />
      </div>

      {showDropdown && (
        <div className="absolute top-full left-0 right-0 z-50 mt-1 max-h-60 overflow-auto rounded-md border bg-popover p-1 shadow-md">
          {filteredOptions.map((option, index) => (
            <div
              key={option.value}
              ref={(el) => { optionsRef.current[index] = el }}
              className={cn(
                "relative flex cursor-pointer items-center rounded-sm px-2 py-1.5 text-sm outline-none",
                index === highlightedIndex ? "bg-accent text-accent-foreground" : "hover:bg-accent hover:text-accent-foreground",
                option.disabled && "pointer-events-none opacity-50"
              )}
              onClick={() => !option.disabled && handleSelect(option.value)}
            >
              {option.label}
            </div>
          ))}
          
          {allowCustom && inputValue.trim() && !filteredOptions.some(opt => opt.label.toLowerCase() === inputValue.toLowerCase()) && (
            <div
              className={cn(
                "relative flex cursor-pointer items-center rounded-sm px-2 py-1.5 text-sm outline-none",
                highlightedIndex === filteredOptions.length ? "bg-accent text-accent-foreground" : "hover:bg-accent hover:text-accent-foreground"
              )}
              onClick={() => handleSelect(inputValue.trim())}
            >
              <span className="text-muted-foreground">Add "</span>
              <span className="font-medium">{inputValue.trim()}</span>
              <span className="text-muted-foreground">"</span>
            </div>
          )}
          
          {filteredOptions.length === 0 && (!allowCustom || !inputValue.trim()) && (
            <div className="text-muted-foreground relative flex items-center justify-center rounded-sm px-2 py-1.5 text-sm">
              No options found
            </div>
          )}
        </div>
      )}
    </div>
  )
}