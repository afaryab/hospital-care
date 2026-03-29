import{c as N,a as b,j as a}from"./utils-q4EKThuO.js";import{B as k}from"./badge-DghL8o_I.js";import{C as _,a as v}from"./card-tJerkyCy.js";import{f as C,g as F}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-BZoqsebz.js";const w={m:"Male",f:"Female",t:"Transgender",o:"Other"},P=j=>{const e=N.c(22),{patient:t,className:x,onClick:m}=j,g=m&&"cursor-pointer hover:shadow-md transition-shadow";let s;e[0]!==x||e[1]!==g?(s=b("cursor-default",g,x),e[0]=x,e[1]=g,e[2]=s):s=e[2];let r;e[3]!==t.name?(r=a.jsx("p",{className:"font-semibold text-sm truncate",children:t.name}),e[3]=t.name,e[4]=r):r=e[4];let n;e[5]!==t.ps_number?(n=a.jsx("p",{className:"text-xs text-muted-foreground font-mono",children:t.ps_number}),e[5]=t.ps_number,e[6]=n):n=e[6];const h=w[t.gender]??t.gender;let o;e[7]!==h?(o=a.jsx(k,{variant:"outline",className:"text-xs",children:h}),e[7]=h,e[8]=o):o=e[8];let c;e[9]!==t.age?(c=t.age!=null&&a.jsxs(k,{variant:"outline",className:"text-xs",children:[t.age," yrs"]}),e[9]=t.age,e[10]=c):c=e[10];let l;e[11]!==o||e[12]!==c?(l=a.jsxs("div",{className:"flex gap-1 flex-wrap mt-1",children:[o,c]}),e[11]=o,e[12]=c,e[13]=l):l=e[13];let i;e[14]!==r||e[15]!==n||e[16]!==l?(i=a.jsx(_,{className:"p-4 flex items-center gap-3",children:a.jsxs("div",{className:"flex flex-col gap-1 min-w-0",children:[r,n,l]})}),e[14]=r,e[15]=n,e[16]=l,e[17]=i):i=e[17];let d;return e[18]!==m||e[19]!==s||e[20]!==i?(d=a.jsx(v,{className:s,onClick:m,children:i}),e[18]=m,e[19]=s,e[20]=i,e[21]=d):d=e[21],d};P.__docgenInfo={description:"",methods:[],displayName:"PatientCard"};const I={title:"Elements/Patient/PatientCard",component:P,tags:["autodocs"],parameters:{layout:"centered"}},p={args:{patient:C}},u={args:{patient:F}},f={args:{patient:C,onClick:()=>alert("Patient clicked")}};p.parameters={...p.parameters,docs:{...p.parameters?.docs,source:{originalSource:`{
  args: {
    patient: mockPatient
  }
}`,...p.parameters?.docs?.source}}};u.parameters={...u.parameters,docs:{...u.parameters?.docs,source:{originalSource:`{
  args: {
    patient: mockPatientFemale
  }
}`,...u.parameters?.docs?.source}}};f.parameters={...f.parameters,docs:{...f.parameters?.docs,source:{originalSource:`{
  args: {
    patient: mockPatient,
    onClick: () => alert('Patient clicked')
  }
}`,...f.parameters?.docs?.source}}};const L=["Default","Female","Clickable"];export{f as Clickable,p as Default,u as Female,L as __namedExportsOrder,I as default};
