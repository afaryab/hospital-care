import{c as C,a as E,j as a}from"./utils-q4EKThuO.js";import{B as I}from"./badge-DghL8o_I.js";import{g as L,f as P}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-BZoqsebz.js";const w={m:"Male",f:"Female",t:"Transgender",o:"Other"},v=y=>{const e=C.c(28),{patient:t,className:x,onClick:s,selected:S}=y,k=S&&"bg-accent",b=s&&"cursor-pointer";let n;e[0]!==x||e[1]!==k||e[2]!==b?(n=E("flex items-center justify-between gap-3 px-3 py-2 rounded-md text-sm","hover:bg-accent transition-colors",k,b,x),e[0]=x,e[1]=k,e[2]=b,e[3]=n):n=e[3];const h=s?"button":void 0,j=s?0:void 0;let r;e[4]!==s?(r=s?_=>_.key==="Enter"&&s():void 0,e[4]=s,e[5]=r):r=e[5];let o;e[6]!==t.name?(o=a.jsx("p",{className:"font-medium truncate",children:t.name}),e[6]=t.name,e[7]=o):o=e[7];let c;e[8]!==t.ps_number?(c=a.jsx("p",{className:"text-xs text-muted-foreground font-mono",children:t.ps_number}),e[8]=t.ps_number,e[9]=c):c=e[9];let i;e[10]!==o||e[11]!==c?(i=a.jsxs("div",{className:"min-w-0",children:[o,c]}),e[10]=o,e[11]=c,e[12]=i):i=e[12];let l;e[13]!==t.age?(l=t.age!=null&&a.jsxs("span",{className:"text-xs text-muted-foreground",children:[t.age," yrs"]}),e[13]=t.age,e[14]=l):l=e[14];const N=w[t.gender]??t.gender;let m;e[15]!==N?(m=a.jsx(I,{variant:"outline",className:"text-xs",children:N}),e[15]=N,e[16]=m):m=e[16];let d;e[17]!==l||e[18]!==m?(d=a.jsxs("div",{className:"flex items-center gap-1.5 shrink-0",children:[l,m]}),e[17]=l,e[18]=m,e[19]=d):d=e[19];let p;return e[20]!==s||e[21]!==d||e[22]!==n||e[23]!==h||e[24]!==j||e[25]!==r||e[26]!==i?(p=a.jsxs("div",{className:n,onClick:s,role:h,tabIndex:j,onKeyDown:r,children:[i,d]}),e[20]=s,e[21]=d,e[22]=n,e[23]=h,e[24]=j,e[25]=r,e[26]=i,e[27]=p):p=e[27],p};v.__docgenInfo={description:"",methods:[],displayName:"PatientListItem"};const M={title:"Elements/Patient/PatientListItem",component:v,tags:["autodocs"],parameters:{layout:"padded"}},u={args:{patient:P}},g={args:{patient:P,selected:!0}},f={args:{patient:L,onClick:()=>alert("Selected")}};u.parameters={...u.parameters,docs:{...u.parameters?.docs,source:{originalSource:`{
  args: {
    patient: mockPatient
  }
}`,...u.parameters?.docs?.source}}};g.parameters={...g.parameters,docs:{...g.parameters?.docs,source:{originalSource:`{
  args: {
    patient: mockPatient,
    selected: true
  }
}`,...g.parameters?.docs?.source}}};f.parameters={...f.parameters,docs:{...f.parameters?.docs,source:{originalSource:`{
  args: {
    patient: mockPatientFemale,
    onClick: () => alert('Selected')
  }
}`,...f.parameters?.docs?.source}}};const T=["Default","Selected","Clickable"];export{f as Clickable,u as Default,g as Selected,T as __namedExportsOrder,M as default};
