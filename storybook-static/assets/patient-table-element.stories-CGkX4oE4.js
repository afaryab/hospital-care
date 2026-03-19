import{c as x,a as g,j as o}from"./utils-q4EKThuO.js";import{B as b}from"./badge-DghL8o_I.js";import{f as h,g as P}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-BZoqsebz.js";const j={m:"Male",f:"Female",t:"Transgender",o:"Other"},u=f=>{const e=x.c(15),{patient:t,className:p}=f;let a;e[0]!==p?(a=g("flex items-center gap-2 min-w-0",p),e[0]=p,e[1]=a):a=e[1];let s;e[2]!==t.name?(s=o.jsx("p",{className:"text-sm font-medium truncate",children:t.name}),e[2]=t.name,e[3]=s):s=e[3];let n;e[4]!==t.ps_number?(n=o.jsx("p",{className:"text-xs text-muted-foreground font-mono",children:t.ps_number}),e[4]=t.ps_number,e[5]=n):n=e[5];let r;e[6]!==s||e[7]!==n?(r=o.jsxs("div",{className:"min-w-0",children:[s,n]}),e[6]=s,e[7]=n,e[8]=r):r=e[8];const d=j[t.gender]??t.gender;let m;e[9]!==d?(m=o.jsx(b,{variant:"outline",className:"text-xs shrink-0",children:d}),e[9]=d,e[10]=m):m=e[10];let i;return e[11]!==a||e[12]!==r||e[13]!==m?(i=o.jsxs("div",{className:a,children:[r,m]}),e[11]=a,e[12]=r,e[13]=m,e[14]=i):i=e[14],i};u.__docgenInfo={description:"Compact inline representation for use as a table cell value.",methods:[],displayName:"PatientTableElement"};const T={title:"Elements/Patient/PatientTableElement",component:u,tags:["autodocs"],parameters:{layout:"padded"}},l={args:{patient:h}},c={args:{patient:P}};l.parameters={...l.parameters,docs:{...l.parameters?.docs,source:{originalSource:`{
  args: {
    patient: mockPatient
  }
}`,...l.parameters?.docs?.source}}};c.parameters={...c.parameters,docs:{...c.parameters?.docs,source:{originalSource:`{
  args: {
    patient: mockPatientFemale
  }
}`,...c.parameters?.docs?.source}}};const w=["Default","Female"];export{l as Default,c as Female,w as __namedExportsOrder,T as default};
