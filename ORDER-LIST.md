# Drone SAR build — ORDER LIST
Compiled 2026-08-31 from the full parts-identification pass. Ready for ordering 2026-09-01.
Course: drones-sar-2026 (Mark4 5" / SpeedyBee F405 V5 / Velox 2207 KV1750 / 6S). Classes start ~21 Sep 2026.

Updated 2026-09-01 (photo check): battery straps confirmed on hand (3x PHISITAL) -> moved to §F.
iMAX B6 has built-in per-cell balance sockets -> §A.3 balance board likely NOT needed. LED-type
and stack-variant buying notes added to §C / §B.
Updated 2026-09-01 (teacher decision): §A.1 changed from "test first" to BUY 2 soldering
stations + tip set outright; existing 2 irons demoted to W2-bench spares.
Updated 2026-09-01: added mid-gauge (20-22AWG) silicone wire assortment to §B — the gap
between the 12AWG and 26AWG already on hand.
Updated 2026-09-01: cart assembled on AliExpress — full line-item record in the new §G.
Awaiting delivery. Two items still PENDING in the cart (gas-stove removal, AA batteries) — see §G.
Updated 2026-09-02 (photo): drop-mechanism servo confirmed 9g SG90/MG90S (2 on hand + metal
horns, §F). School has a Creality Ender-3 V3 3D printer. §B payload item -> just needs the STL
printed; nothing to buy. Beginner print walkthrough recorded in §B.
Updated 2026-09-03 (photo): 6x Gemfan Hurricane 51433-3 (5.1x4.3x3, PC, purple, 2L2R) on hand
= 6 quad sets / 24 props. Fit the Mark4 5" + Velox 2207 (5mm bore). §E wrong-prop issue resolved;
§D / §F prop lines updated.
Updated 2026-09-03 (equipment photos): existing bench kit inventoried. On hand & confirmed ->
§F: blue silicone heat mat (~45x30cm), 2x "third hand" (Pro'sKit SN-390 PCB holder + magnifier
helping-hands), ~115-in-1 precision screwdriver bit set + pry tools, precision flush cutter
(<=~16AWG), 4x ESD tweezers, hot glue gun, 2x basic pencil soldering irons (~30-60W, W2 spares).
New gaps added to §B/§D: wire strippers, a full-size diagonal cutter (for 12AWG), desoldering
pump + braid, ball-end hex drivers, fume extraction, per-bench flush cutters/stands.
Updated 2026-09-03 (Κεφ.14β video 1 review): presenter's kit vs ours — the adjustable 300-400C
iron IS covered (2x stations, §G). NOT ordered / now in §B: brass-wool tip cleaner x2, a 2nd
flux (pen or gel tub), practice perfboards. Student solder-practice kits added to §D.
Updated 2026-09-03 (orders placed - see rewritten §G): the §A.1 cart is confirmed ORDERED, plus
a 2nd batch of ~16 bench-tool orders went out the same day. §B/§C/§D checkboxes flipped to
ORDERED where matched. STILL not ordered (sitting in the AliExpress cart): FA-400 fume extractor
x2 (SAFETY, "only 2 left"), zip ties, CNHL 6S LiPo spare, a redundant 3rd ESPLB mat, and a
DUPLICATE heat-shrink kit that must be deleted before checkout. AA alkaline batteries -> buy
locally. One 2026-09-01 order (EUR 15.91, "Shop1104334934") still unidentified - check it.

===================================================================
## A. BLOCKING — must be resolved before the build can run
===================================================================
1. [ORDERED 2026-09-01 - see §G] 2x temperature-controlled soldering STATIONS (mains).
   Spec: adjustable 200-480C, >=60W (80-90W better for XT60/12AWG), ceramic heater,
   900M tip standard, EU 220-240V plug (NOT a 110V US unit), includes stand + tip cleaner.
   ~20-35 EUR each.  Search: "soldering station 900M 60W adjustable temperature 220V"
   + spare tips: "900M soldering tip set 10pcs" x1, ~6-10 EUR — must include large
   chisel/bevel (T-K, T-3C, T-4C) for XT60/12AWG + a fine T-2C / 1.6mm for the LED joints.
   Working temp: ~320-350C for the leaded Sn63Pb37 on hand; ~360-390C if switched to lead-free.
   The 2 irons already on hand (photo 2026-09-03: basic ~30-60W pencil irons, one possibly
   adjustable-temp, neither a station): still TEST them (heat to 350C, solder an XT60 in <6 s).
   If OK they become extra irons for the W2 LED-practice benches, NOT the primary build irons —
   and each needs its own safe stand+sponge on a school desk (see §D).
2. [CHECK] LED strip (drone arm LEDs for W6) — not found in the box.
   RECOMMENDATION: drop it from scope — it's decorative, not needed to fly.
   If you still want it: WS2812B 5V addressable strip, ~3 EUR.  Search: "WS2812 5V FPV drone LED strip"
3. [LIKELY RESOLVED - 2026-09-01 photo] 6S balance board.
   The blue iMAX B6 on hand has BUILT-IN per-cell balance sockets (legend "2/3/4/5/6 cells"
   printed on top). Confirm the OUTPUT edge has a ROW of ~5 white JST-XH sockets -> then no
   board needed, plug the 6S LiPo's balance lead straight into the "6S" socket.
   ONLY if that edge has a single balance port instead: buy "iMAX B6 balance board 2-6S
   JST-XH", ~2 EUR.

(Already confirmed present: RadioMaster TX12 radio, 4x motors, B6 mains power brick, LiPo-safe bag.)

===================================================================
## B. TO BUY — build parts (AliExpress)
===================================================================
[x] ORDERED 2026-09-01 (see §G) — ⭐ BACKUP flight stack — 2nd SpeedyBee F405 V5 (Deluxe) stack, SAME model as the build
    stack in §F. Buy the matched FC + 4-in-1 ESC PAIR, not a bare FC — every heavy solder
    joint (4 motors, XT60 battery lead, capacitor) is on the ESC board, so a bare FC
    rehearses none of the joints that matter. ~55-75 EUR.  Search: "SpeedyBee F405 V5 stack 50A"
    Variant check: pick the FULL stack (FC + 4in1 ESC on 30.5x30.5 standoffs), ESC 50A or 55A
    BLHeli_S, 3-6S rated. If a listing name is unclear (e.g. a bare code, not "stack"/"ESC"),
    skip it. Do NOT buy a listing that is only the FC.
    Three jobs from one buy:
      1. Solder-rehearsal for the teacher BEFORE W1 — do the W4/W5 joints once on this spare,
         not on the class's only drone in week 4. Confirms the iron is hot enough for
         6S-gauge wire and that the process works end to end.
      2. Betaflight + GPS Rescue dry-run for W8 — config over USB is non-destructive; run the
         whole session freely, then leave the working config on it as a reference.
      3. W7 first-power-on BACKUP — if a group shorts the board or a cold W4 joint kills the
         ESC under load, swap in the pre-soldered spare instead of losing the donation drone
         4 weeks before the conference with no fast reorder.
    Afterwards: keep as a classroom probe/reference board (students meter it without touching
    the live drone); carries to next year.
    Limit: covers FC/ESC failure only — not a dead motor or a cracked frame.
[ ] Payload drop mechanism — NOTHING TO BUY. Servo confirmed 9g SG90/MG90S (2 on hand + metal
    horns, §F); school has a Creality Ender-3 V3. You only have the servo (the motor) — the
    hook/latch gets 3D-printed. Status: print pending.
    Files (free): printables.com/model/1326088-payload-dropper-mechanism ; or search Printables /
      cults3d for "SG90 payload release" / "FPV drop mechanism SG90". Pick an SG90-fit design
      (2 parts: base + rotating latch).
    Material: white PLA already loaded is fine for a prototype + light demo payload. PETG only if
      a real weight (water bottle) is ever dropped. Print base x1 + latch x3 (latch is what breaks).
    Slicer (Creality Print, Ender-3 V3 profile): 0.20mm layer, 4 walls, 40% infill, brim ON,
      supports OFF unless preview shows >45 deg overhangs, PLA 210C / bed 60C. Export G-code to a
      FAT32 USB stick; print from the printer's screen (bed leveling is automatic on the V3).
    First layer is the only thing to babysit: lines must merge flat and stick; if they don't,
      Tune -> Z-offset -0.05mm per step (too low / scraping -> +0.05mm).
    Assembly: centre the servo first (mid position via FC / tester), then screw the printed latch
      to the metal horn at the right angle; base zip-tied/screwed under the frame plate; 3-wire
      lead -> FC servo/AUX pad, 5V from the UBEC 6A.
    Full beginner walkthrough: see chat 2026-09-02 (this session).
    IF THE PRINT FAILS repeatedly: buy the MG996-size "parabolic switch releaser" + 1x MG996R
      servo (~8 EUR total). Heavier (~75g + servo) but complete and in stock. The drone can carry it.
[x] ORDERED 2026-09-01 (see §G) — Battery pad — "lipo battery non slip pad 3M silicone" (~40x95mm, 3M backing), 1 pack. ~2-4 EUR.
    NOT the same as the straps: this is the anti-slip pad the LiPo sits ON; the straps hold it
    down. Straps are already on hand (§F) - still need this pad.
[x] Battery straps — DONE, on hand: 3x PHISITAL (confirmed 2026-09-01 photo). Enough for the
    build + spares. Moved to §F. Optional: a 5-pack for margin is ~2 EUR.
[x] ORDERED 2026-09-01 (see §G) — Micro SD card ~32GB (class 10 / U1) — for the goggle DVR (W8/W9 flight-video uploads). ~5 EUR.
[x] ORDERED 2026-09-01 (see §G) — More solder — "Sn63Pb37 0.8mm rosin solder 250g" x1. ~5-8 EUR.
[x] ORDERED 2026-09-01 (see §G) — Mid-gauge silicone wire assortment — fills the gap between the 12AWG (power) and 26AWG
    (fine signal) already on hand in §F. ~20-22AWG, 6-colour box, ~5 m each. ~6-9 EUR.
    Search: "silicone wire kit 22AWG 6 colors"
    For: servo-lead extensions (payload drop mechanism), rewiring GPS/RX/VTX/buzzer when the
    factory pigtails are too short, camera/VTX power (26AWG is marginal on 6S). One box covers
    the whole build; no separate 16AWG needed (12AWG covers power, motors come pre-wired).

--- HAND TOOLS not found in the 2026-09-03 equipment photos (needed for the build itself) ---
[x] ORDERED 2026-09-03 (see §G) — Wire strippers — "Automatic Wire Stripper AWG24-10" x2. Range
    covers the build (12-22AWG silicone wire, servo leads, W2 hookup wire). ~25 EUR.
[x] ORDERED 2026-09-03 (see §G) — Full-size diagonal cutter — Deli CR-V 5" diagonal pliers x2.
    For the 12AWG battery lead + large zip ties (the precision flush cutter on hand maxes ~16AWG).
[x] ORDERED 2026-09-03 (see §G) — Desoldering pump x2 (aluminium solder sucker) + desoldering
    braid/wick 2.0mm x3 rolls.
[x] ORDERED 2026-09-03 (see §G) — Brass-wool tip cleaner x2 (Silver, with base). Cleans the tip
    without the thermal shock of a wet sponge; what the Κεφ.14β video shows.
[x] ORDERED 2026-09-03 (see §G) — 2nd flux — NC-559-ASM no-clean tacky gel flux, 10cc x2. Adds
    to the 1 flux pen already on hand (§F).
[x] ORDERED 2026-09-03 (see §G) — Practice perfboards — double-sided prototype PCB (5x7cm) x2.
    Controlled practice joints before real hardware; also for student reps beyond the W2 LED.

===================================================================
## C. TO BUY — W2 "Solder the LED" practice materials (sized for 100 students)
===================================================================
CONSUMED (1 per student, buy with margin):
[x] ORDERED 2026-09-01 (see §G) — 5mm LEDs — 2x 100-pack red = 200 pcs. ~6 EUR.
    PLAIN single-colour, 2-LEG LEDs. NOT 3-leg "bicolor / two-color / common anode-cathode"
    packs — the extra leg forces a polarity puzzle on the student's first-ever solder joint,
    which is exactly the friction W2 is meant to avoid. Search: "5mm LED diode assorted kit
    200pcs" (diffused, 5 colours).
[x] ORDERED 2026-09-01 (see §G) — 220 ohm 1/4W resistors — 200 pcs (2x 100-pack, variant "220R"). ~2 EUR.  (use 150 ohm if using NiMH rechargeable AAs)
[x] ORDERED 2026-09-01 (see §G) — Red silicone hookup wire 24AWG — x2 (~20 m). ~3 EUR.  (black 26AWG already on hand)
REUSABLE (shared across all sections + future years):
[x] ORDERED 2026-09-01 (see §G) — 2xAA battery holders with wire leads — ~30 pcs (covers the largest single section + margin).
    Buy 100 only if you want every student to keep a permanent personal kit. ~10-15 EUR for 30.
[!] STILL TO BUY — AA alkaline batteries — ~100 (a 100-pack), 2 per holder + spares. ~15 EUR.
    Required for the W2 LED exercise. NOT ordered on AliExpress on purpose — buy locally
    (cheaper, faster, no customs). A supermarket/hardware multipack is fine.

===================================================================
## D. OPTIONAL / nice-to-have (not blocking)
===================================================================
- Multimeter — have 1; [x] ORDERED 2026-09-03 (see §G) a 2nd (one per bench). W7 short-check.
  Confirm the one on hand has a 9V battery.
- [x] CORRECTED 2026-09-17: only have **1x Pro'sKit SN-390** (PCB *vise/clamp* + magnifier
  arm — clamps a single flat board or wire steady, NOT alligator-clip hands) **+ 1x separate
  basic alligator-clip "3rd hand"** (this one has the clip arms for holding 2 free wire ends
  together). 2 different tools, 1 each, NOT 2x SN-390 as previously logged. **No purchase
  needed** — solved by matching technique to tool per bench (§C): the alligator-clip bench
  does the LED exercise free-air; the SN-390 bench clamps a perfboard instead. Both benches
  run in parallel, just with different soldering technique.
- 2nd-3rd VIFLY ShortSaver 2 smoke stopper — have 1 (enough); more avoids a W7 bottleneck.
- [x] ORDERED 2026-09-03 (see §G) — 2nd flux — see §B (NC-559-ASM gel flux x2).
- [x] ORDERED 2026-09-03 (see §G) — Solder-practice kits — NE555 blinking-LED kit x5. Controlled
  student soldering reps beyond the W2 LED; pairs with the §B perfboards.
- [x] ORDERED 2026-09-03 (see §G) — Soldering iron stands — JCD 820 x2, safe stands for the 2
  basic pencil irons on the W2 benches (the 2 new stations ship with their own).
- [x] ORDERED 2026-09-03 (see §G) — Ball-end hex drivers — RC Tools 1.5/2.0/2.5/3.0mm set x2,
  for threadlocked M3 frame bolts (faster / less cam-out than the ~115-in-1 bit set on hand).
- [x] ORDERED 2026-09-03 (see §G) — Per-bench hand tools — mini diagonal cutters x4 (LED/resistor
  leg trimming) + needle-nose pliers x2 (SAMZHE).
- Silicone heat mat — have 1 on hand + [x] 1 ORDERED 2026-09-01 (ESPLB E-6, see §G) = one per
  bench, DONE. A 3rd ESPLB mat is still sitting in the cart — redundant, skip it (or keep as a
  spare only if free-ish).
- [ ] STILL TO ORDER — Fume extraction — SAFETY. FA-400 fume extractor x2 (~73 EUR) is in the
  AliExpress cart but NOT ordered; stock showed "only 2 left". Order it, or substitute a plain
  bench fan per bench as a stopgap. Do not run the class with no fume handling.
- [ ] STILL TO ORDER — Zip ties — ~12 EUR assorted pack, in the cart, NOT ordered. For wire
  management + zip-tying the printed drop mechanism to the frame.
- Flight-week spares: 2-4 more CNHL 6S packs + a 2nd/faster charger (ISDT Q6 / HOTA D6). Have 2
  packs + iMAX B6 (slow: ~1 pack/hour). One CNHL 6S pack (~60 EUR) is in the cart, not ordered
  — optional, decide by budget.
- 1-2 spare 5.8GHz RHCP drone antennas — have 3 already, only if crashes eat them.
- More 5" props — have 6x Gemfan Hurricane 51433-3 sets (24 props, confirmed 2026-09-03).
  Enough for one airframe across the flight weeks (6 full prop changes). A student class eats
  props on crashes, so 3-6 more sets is cheap insurance. 51433 (4.3 pitch) is a bit milder than
  the 5152S-3 originally planned = smoother, cooler, more efficient — fine, arguably better for
  training. Recount after W8-W10.

===================================================================
## E. ISSUES TO CHASE (not orders)
===================================================================
- 7" props (Gemfan Flash 7040-3, 2+ bags) — WRONG SIZE, do not fit a 5" frame. RESOLVED as a
  blocker: 6x Gemfan Hurricane 51433-3 (5") are now on hand and cover the build (see §D/§F). Still
  worth a note to the supplier / return if easy, but no longer needs a replacement order. Keep the
  7" bags in a "DO NOT USE" box.
- Leaded solder policy — the solder on hand is Sn63Pb37 (contains lead). Confirm your school allows
  leaded solder for students; if not, switch the whole class to lead-free (needs a hotter iron).

===================================================================
## F. ALREADY COVERED — do NOT re-order
===================================================================
Frame (Mark4 5"), FC+ESC stack (SpeedyBee F405 V5 Deluxe) + all cables/pigtails, capacitor, XT60
pigtail + 5 pairs XT60 spares, VTX (RUSH Tank Solo) + 3 drone antennas (2x Foxeer Lollipop 4 MMCX
+ 1x TrueRC X-AIR SMA, all RHCP), camera (Caddx Ratel 2) + mounts, RX (HappyModel EP1 Dual),
GPS (Matek M10Q) + mount, goggles (Eachine EV800DM) + omni & patch antennas, 2x 6S LiPo,
iMAX B6 charger + power brick, LiPo-safe bag, 6x 5" prop sets (24 props, Gemfan Hurricane
51433-3, 5.1x4.3x3 PC purple), UBEC 6A, VIFLY Finder 2 buzzer,
VIFLY ShortSaver 2 smoke stopper, conformal coating (2 bottles), threadlocker 243, flux pen,
solder (100g - topping up in B), 12AWG + 26AWG wire, servos x2 + metal servo horns (2 styles),
soldering stand + tip cleaner, multimeter, battery straps (3x PHISITAL),
iMAX B6 charge lead set (XT60 + JST + banana + barrel adapters).
Bench kit (confirmed by photo 2026-09-03, CORRECTED 2026-09-17): blue silicone heat mat
~45x30cm; **1x Pro'sKit SN-390 PCB holder w/ magnifier + solder-spool spring, plus a separate
1x basic 3rd-hand alligator clip** (2 different tools, 1 each - not 2x SN-390); ~115-in-1
precision screwdriver bit set (incl. hex 1.5/2.0/2.5mm for M2/M3, Phillips, Torx) + plastic
pry tools/spudgers; precision flush cutter (rated ~1.3mm Cu / ~16AWG - fine work only);
4x ESD tweezers (2 straight fine, 1 curved, 1 flat SMD); hot glue gun; 2x basic pencil
soldering irons ~30-60W (-> W2-bench spares, see §A.1).

===================================================================
## G. AliExpress ORDERS — status as of 2026-09-03
===================================================================
Deadlines: W2 consumables must land before ~28 Sep; stack + stations before ~21 Sep (teacher's
pre-W1 solder rehearsal). Verify each order's own delivery estimate against these.

--- G.1  ORDERED 2026-09-01 (status: "Awaiting delivery" / some "Confirm received") ---
- SpeedyBee F405 V5 OX32 55A 30x30 FC+ESC stack, Standard Version - x1, ~EUR 113 (FlyPro FPV
  Store). [§B backup stack]
- Soldering station 80W 936R, adjustable temp + LCD, 220V EU plug - x2, EUR 68.20. [§A.1]
- 900M-T soldering tips, 13pc mix pack - x1, EUR 9.99. [§A.1]
- ESPLB solder wire Sn63Pb37 0.8mm, 100g - x3 = 300g, EUR 30.15. (qty 3 / 0.8mm confirmed.) [§B]
- Silicone wire kit 22AWG, 6 colours (AuHo) - x1, EUR 12.09. [§B mid-gauge]
- SanDisk Ultra microSD 32GB - x1, EUR 13.20. [§B]
- 2xAA battery holders, flying leads - x30, EUR 16.94. [§C]
- Red silicone hookup wire 24AWG, 10m - x2 (20m), EUR 8.48. [§C]
- Battery non-slip silicone pad, 20x25mm 10pcs - x1, EUR 6.17. [§B]
- ESPLB silicone heat mat (E-6) - x1, EUR 17.56. [§D 2nd mat - one per bench with the one on hand]
- 5mm LED assorted kit, "5mm Red 100pcs" - x2 = 200 pcs, EUR 8.02 (JXDZXL). [§C]
- 220R carbon-film resistors, 100pcs - x2 = 200 pcs, EUR 6.25 (shenzhen IC chip). [§C]
- [MISTAKE] Stainless steel camping gas stove - x1, EUR 6.68. Was never removed; rode in with
  the SpeedyBee bundle. Harmless - just ignore it when it arrives.
- [UNIDENTIFIED] one order EUR 15.91, seller "Shop1104334934" - product name did not render in
  the orders PDF. Open it and confirm what it is (possibly AA batteries or a duplicate).

--- G.2  ORDERED 2026-09-03 (status: "Processing" / several "Verifying your payment") ---
16 bench-tool orders. VERIFY the "Verifying your payment" ones actually cleared - they cancel if
payment doesn't complete.
- Aluminium desoldering pump (solder sucker) - x2, EUR 7.94. [§B]
- Desoldering braid/wick 2.0mm 1.5m - x3, EUR 10.05. [§B]
- JCD 820 soldering iron stand holder - x2, EUR 16.84. [§D - stands for the 2 W2 pencil irons]
- SAMZHE pointed/needle-nose pliers - x2, EUR 13.85. [§D]
- NC-559-ASM no-clean gel flux, 10cc - x2, EUR 14.22. [§B 2nd flux]
- Automatic wire stripper AWG24-10 - x2, EUR 25.49. [§B]
- NE555 blinking-LED solder-practice kit - x5, EUR 15.07. [§D]
- Heat-shrink tubing kit 2:1, "530PCS (220V EU Plug)" - x1, EUR 16.09.  NOTE: this is the
  variant WITH a hot-air gun; the cheaper "530PCS with bag" EUR 5.34 is a DUPLICATE still in the
  cart - DELETE it before any further checkout.
- LiPo voltage tester / 1-8S alarm - x2, EUR 13.54.
- Deli diagonal pliers CR-V 5" - x2, EUR 12.47. [§B full-size cutter]
- Mini diagonal pliers / wire cutter 4"/6" - x4, EUR 14.01. [§D leg-trimming]
- Digital multimeter - x1. [§D 2nd meter, one per bench]
- Double-sided prototype PCB / perfboard 5x7cm - x2. [§B]
- Kapton polyimide tape, Brown 33m 0.05mm 10mm - x2, EUR 8.68.
- Brass-wool tip cleaner, Silver with base - x2, EUR 11.08. [§B]
- RC Tools hex driver set 1.5/2.0/2.5/3.0mm, Black - x2, EUR 22.72. [§D ball-end hex]

--- G.3  STILL IN THE CART, NOT ORDERED ---
- [ ] FA-400 fume extractor - x2, ~EUR 73. SAFETY (§D). Stock showed "only 2 left" - order soon
      or substitute a plain bench fan per bench.
- [ ] Zip ties, assorted - ~EUR 12.86. (§D)
- [ ] DELETE: duplicate heat-shrink kit "530PCS with bag" EUR 5.34 - the WITH-gun kit is already
      ordered (G.2). Checking out the cart as-is buys a 2nd kit.
- [ ] SKIP: 3rd ESPLB silicone mat, ~EUR 13.34 - redundant (1 on hand + 1 ordered in G.1 = one
      per bench). Only keep it as a spare if near-free.
- [ ] OPTIONAL: CNHL 6S LiPo pack, ~EUR 59 - flight-week spare, decide by budget. (§D)

--- G.4  BUY LOCALLY (not AliExpress) ---
- AA alkaline batteries ~100 (100-pack) - W2 LED exercise. Supermarket/hardware multipack.
- Isopropyl alcohol (IPA) - flux cleanup + 3D-printer bed cleaning. Pharmacy / paint shop.

--- G.5  NOT ordered (deliberately) ---
- Payload drop mechanism -> 3D-print at school. [§B]
- iMAX B6 6S balance board -> the B6 has built-in per-cell sockets. [§A.3]
- LED arm strip -> dropped from scope. [§A.2]
- Faster LiPo charger, extra "third hands", extra smoke stoppers -> decide later. [§D]

--- G.6  CONFIRMED ARRIVED 2026-09-16 (photo-verified) ---
From G.1: 900M-T soldering tips; ESPLB solder wire x3 spools; SanDisk microSD 32GB;
battery non-slip pad; 2nd silicone heat mat ("Repair Pad").
From G.2: aluminium desoldering pump x2; SAMZHE needle-nose pliers x2; NC-559-ASM gel flux x2;
automatic wire stripper x2; NE555 LED practice kit (4 of 5 counted in photo - check the 5th);
LiPo voltage tester (only 1 of 2 visible in photo - check the 2nd); Deli diagonal pliers x2;
mini diagonal pliers/cutter; double-sided perfboard x2; brass-wool tip cleaner x2.

⚠️ Two things to double-check physically (not urgent, just verify next time you're at the box):
- The "Deli diagonal pliers" package is printed **"PLASTIC NIPPERS" (DL0305A)** — that's a
  cutter meant for plastic sprue, not the CR-V metal wire cutter that was ordered. Might be a
  wrong-item substitution by the seller, or just misleading packaging text on a normal metal
  cutter. Check the jaws are metal-rated before using it on 12AWG/servo wire.
- A pair of small **2.0mm solder wire spools (1.5m each)** showed up bundled with the flux
  paste order - not on the original order line (which was 0.8mm x3=300g, separately confirmed
  arrived above). Harmless bonus/sample, most likely came free with the NC-559 flux kit.

--- G.7  CONFIRMED ARRIVED 2026-09-16, second batch same day (photo-verified) ---
From G.1: SpeedyBee F405 V5 OX32 55A backup stack; red silicone hookup wire 24AWG x2 (20m);
2xAA battery holders x30 (bag hand-labelled "Ω"); 220R resistors x200 (loose, hand-labelled
"220R").
From G.2: heat-shrink tubing kit incl. heat gun (box says "Heat Gun", matches the WITH-gun
variant that was ordered - good, confirms the right one shipped, not the duplicate); digital
multimeter (ANENG High Precision, model SZ304/SZ305 range shown on box).

--- G.8  CONFIRMED ARRIVED 2026-09-16, third batch same day (photo-verified) ---
From G.1: 5mm LED ×200 (loose, red); silicone wire kit 22AWG 6 colours.
From G.1: **both 936R soldering stations** — photo showed a digital adjustable-temp iron
handle (buttons/display on the handle itself) matching "adjustable temp + LCD"; teacher
confirmed identification. **This was the single blocking item for Εβδ. 2 - now cleared.**

**Still NOT confirmed arrived** (not in any photo yet - unchanged 🚚 on the Moodle status page
and here): JCD iron stand holders x2; Kapton tape x2; RC Tools hex driver set; the
camping-gas-stove mistake item; the unidentified EUR 15.91 order. None of these block Εβδ. 2 -
everything needed for the soldering-practice week is now confirmed on hand. Locally-bought
items (AA alkaline batteries, isopropyl alcohol, §G.4) are separate from AliExpress and
untracked here either way.

**Moodle status page** (`📦 Υλικά Build — Κατάσταση`) now also shows a **Εβδ. (week) column**
next to the drone-parts and soldering-equipment tables, so students can see which week each
item is actually needed for — added 2026-09-16 per the teacher's request.
